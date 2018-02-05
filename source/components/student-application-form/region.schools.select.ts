import { NgRedux } from "@angular-redux/store";
import { Component, OnDestroy, OnInit } from "@angular/core";
import { Injectable } from "@angular/core";
import { FormArray, FormBuilder, FormControl, FormGroup } from "@angular/forms";
import { Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";

import { RegionSchoolsActions } from "../../actions/regionschools.actions";
import { IEpalClassRecords } from "../../store/epalclasses/epalclasses.types";
import { REGION_SCHOOLS_INITIAL_STATE } from "../../store/regionschools/regionschools.initial-state";
import { IRegionRecords } from "../../store/regionschools/regionschools.types";
import { ISectorRecords } from "../../store/sectorcourses/sectorcourses.types";
import { ISectorFieldRecords } from "../../store/sectorfields/sectorfields.types";
import { IAppState } from "../../store/store";

@Component({
    selector: "region-schools-select",
    template:
    `
    <div class="row">
             <breadcrumbs></breadcrumbs>
    </div>

    <div class = "loading" *ngIf="!(regions$ | async) || (regions$ | async).size===0">
    </div>
    <!-- <div class="row equal">
      <div class="col-md-12"> -->

      <div id="choiceSentNotice" (onHidden)="onHidden()" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header {{modalHeader | async}}" >
                <h3 class="modal-title pull-left"><i class="fa fa-check-square-o"></i>&nbsp;&nbsp;{{ modalTitle | async }}</h3>
              <button type="button" class="close pull-right" aria-label="Close" (click)="hideModal()">
                <span aria-hidden="true"><i class="fa fa-times"></i></span>
              </button>
            </div>
            <div class="modal-body">
                <p>{{ modalText | async }}</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Κλείσιμο</button>
            </div>
          </div>
        </div>
      </div>

<div style="min-height: 500px;">
      <h4> Επιλογή Σχολείου</h4>
       <form [formGroup]="formGroup">
        <div formArrayName="formArray">
        <p style="margin-top: 20px; line-height: 2em;"> Παρακαλώ επιλέξτε εως τρία ΕΠΑΛ στα οποία επιθυμεί να φοιτήσει ο μαθητής.
         Επιλέξτε πρώτα την Περιφερειακή Διεύθυνση στην οποία ανήκει το σχολείο της επιλογής σας,στη συνέχεια τα σχολεία και τέλος πατήστε <i>Συνέχεια</i>.
        Μπορείτε να επιλέξετε απο ένα εως τρία σχολεία που δύναται να ανήκουν σε περισσότερες απο μια Περιφερειακές Διευθύνσεις.</p>
            <ul class="list-group main-view">
            <div *ngFor="let region$ of regions$ | async; let i=index; let isOdd=odd; let isEven=even"  >
                <li class="list-group-item isclickable" (click)="setActiveRegion(i)" [class.oddout]="isOdd" [class.evenout]="isEven" [class.selectedout]="regionActive === i">
                    <h5>{{region$.get('region_name')}}</h5>
                </li>

                <div *ngFor="let epal$ of region$.get('epals'); let j=index; let isOdd2=odd; let isEven2=even" [class.oddin]="isOdd2" [class.evenin]="isEven2" [hidden]="i !== regionActive">

                        <div class="row">
                            <div class="col-md-2 col-md-offset-1">
                                <input #cb *ngIf = "(numSelected | async) !== 3 || epal$.get('selected')" type="checkbox" formControlName="{{ epal$.get('globalIndex') }}"
                                (change)="saveSelected(cb.checked,i,j)" >

                             </div>
                            <div class="col-md-8  col-md-offset-1 isclickable">
                                {{epal$.get("epal_name") | removeSpaces}}
                            </div>
                        </div>
                </div>

            </div>
            </ul>
        </div>
        <div class="row" style="margin-top: 20px; margin-bottom: 20px;" *ngIf="(regions$ | async)">
        <div class="col-md-6">
            <button type="button" class="btn-primary btn-lg pull-left isclickable" (click)="navigateBack()" >
          <i class="fa fa-backward"></i>
            </button>
        </div>
        <div class="col-md-6">
            <button type="button" class="btn-primary btn-lg pull-right isclickable" style="width: 9em;" (click)="navigateToApplication()" >
                <span style="font-size: 0.9em; font-weight: bold;">Συνέχεια&nbsp;&nbsp;&nbsp;</span><i class="fa fa-forward"></i>
            </button>
        </div>
        </div>
    </form>
<!--     <pre>{{formGroup.value | json}}</pre> -->
    </div>
  `
})
@Injectable() export default class RegionSchoolsSelect implements OnInit, OnDestroy {
    private regions$: BehaviorSubject<IRegionRecords>;
    private epalclassesSub: Subscription;
    private regionsSub: Subscription;
    private sectorsSub: Subscription;
    private sectorFieldsSub: Subscription;

    private formGroup: FormGroup;
    private rss = new FormArray([]);
    private classActive = "-1";
    private regionActive = <number>-1;
    private regionActiveId = <number>-1;
    private courseActive = <number>-1;
    private numSelected: BehaviorSubject<number>;
    private selectionLimit: BehaviorSubject<number>;
    private selectionLimitOptional: BehaviorSubject<boolean>;
    private regionSizeLimit = <number>3;
    private classNight: BehaviorSubject<boolean>;

    private modalTitle: BehaviorSubject<string>;
    private modalText: BehaviorSubject<string>;
    private modalHeader: BehaviorSubject<string>;


    constructor(private fb: FormBuilder,
        private _rsa: RegionSchoolsActions,
        private _ngRedux: NgRedux<IAppState>,
        private router: Router

    ) {
        this.regions$ = new BehaviorSubject(REGION_SCHOOLS_INITIAL_STATE);
        this.formGroup = this.fb.group({
            formArray: this.rss

        });

        this.numSelected = new BehaviorSubject(0);
        this.selectionLimit = new BehaviorSubject(3);
        this.selectionLimitOptional = new BehaviorSubject(false);
        this.classNight = new BehaviorSubject(false);

        this.modalTitle = new BehaviorSubject("");
        this.modalText = new BehaviorSubject("");
        this.modalHeader = new BehaviorSubject("");

    };

    ngOnInit() {

        (<any>$("#choiceSentNotice")).appendTo("body");

        this.selectEpalClasses();

        this.selectRegionSchools();
    }

    ngOnDestroy() {

        (<any>$("#choiceSentNotice")).remove();

        if (this.epalclassesSub) {
            this.epalclassesSub.unsubscribe();
        }
        if (this.regionsSub) {
            this.regionsSub.unsubscribe();
        }
        if (this.sectorsSub) {
            this.sectorsSub.unsubscribe();
        }
        if (this.sectorFieldsSub) {
            this.sectorFieldsSub.unsubscribe();
        }

    }

    public showModal(): void {
        (<any>$("#choiceSentNotice")).modal("show");
    }

    public hideModal(): void {
        (<any>$("#choiceSentNotice")).modal("hide");
    }

    public onHidden(): void {
        // this.isModalShown.next(false);
    }

    selectEpalClasses() {
        this.epalclassesSub = this._ngRedux.select("epalclasses")
            .map(epalClasses => <IEpalClassRecords>epalClasses)
            .subscribe(ecs => {
                if (ecs.size > 0) {
                    ecs.reduce(({}, epalclass, i) => {
                        this.setClassActive(epalclass.get("name"));
                        if (epalclass.get("name") === "4") {
                            this.classNight.next(true);
                        }
                        this.getAppropriateSchools(epalclass.get("name"));
                        return epalclass;
                    }, {});
                }
            }, error => { console.log("error selecting epalclasses"); });
    }

    selectRegionSchools() {

        this.regionsSub = this._ngRedux.select("regions")
            .subscribe(regions => {
            //.subscribe(regions =>  setTimeout(() =>  {
                let rgns = <IRegionRecords>regions;
                let numsel = 0;
                let numreg = 0;   // count reduced regions in order to set activeRegion when user comes back to his choices
                this.selectionLimitOptional.next(false);
                let pushControls = false;
                if (this.rss.length === 0)
                    pushControls = true;
                rgns.reduce((prevRegion, region) => {
                    numreg++;
                    region.get("epals").reduce((prevEpal, epal) => {
                        if (pushControls)
                            this.rss.push(new FormControl(epal.get("selected"), []));
                        if (epal.get("selected") === true) {
                            numsel++;
                            //this.numSelected.next(numsel);
                            if (epal.get("epal_special_case") === "1") {
                                this.selectionLimitOptional.next(true);
                            }
                            this.regionActiveId = parseInt(region.region_id);
                            this.regionActive = numreg - 1;
                        }
                        if (parseInt(region.region_id) === this.regionActiveId) {
                            if (region.get("epals").size < this.regionSizeLimit)
                                this.selectionLimitOptional.next(true);
                        }
                        return epal;
                    }, {});

                    return region;
                }, {});

                this.numSelected.next(numsel);
                this.regions$.next(rgns);
          }, error => { console.log("error selecting regions"); });

    }

    setClassActive(className) {
        this.classActive = className;
    }

    getAppropriateSchools(epalClass) {

        if (epalClass === "1") {
            this._rsa.getRegionSchools(1, "-1", false);
        }
        else if (epalClass === "2") {
            this.sectorFieldsSub = this._ngRedux.select("sectorFields")
                .map(sectorFields => <ISectorFieldRecords>sectorFields)
                .subscribe(sfds => {
                    sfds.reduce(({}, sectorField) => {
                        if (sectorField.selected === true) {
                            this.courseActive = sectorField.id;
                            this._rsa.getRegionSchools(2, this.courseActive, false);
                        }
                        return sectorField;
                    }, {});
                },
                error => {
                    console.log("Error Selecting Sector Fields");
                });
        }
        else if (epalClass === "3" || epalClass === "4") {

            this.sectorsSub = this._ngRedux.select("sectors")
                .map(sectors => <ISectorRecords>sectors)
                .subscribe(sectors => {
                    sectors.reduce((prevSector, sector) => {
                        if (sector.get("sector_selected") === true) {
                            sector.get("courses").reduce((prevCourse, course) => {
                                if (course.get("selected") === true) {
                                    this.courseActive = parseInt(course.get("course_id"));
                                    // this._rsa.getRegionSchools(3,this.courseActive, false);
                                    this._rsa.getRegionSchools(parseInt(epalClass), this.courseActive, false);
                                }
                                return course;
                            }, {});
                        }
                        return sector;
                    }, {});
                }, error => console.log("error selecting loginInfo"));
        }
    }

    navigateBack() {
        if (this.classActive === "1") {
            this.router.navigate(["/epal-class-select"]);
        }
        else if (this.classActive === "2") {
            this.router.navigate(["/sector-fields-select"]);
        }
        else if (this.classActive === "3" || this.classActive === "4") {
            this.router.navigate(["/sectorcourses-fields-select"]);
        }
    }

    setActiveRegion(ind) {
        if (ind === this.regionActive)
            ind = -1;
        this.regionActive = ind;
    }

    saveSelected(checked, i, j) {
        this._rsa.saveRegionSchoolsSelected(checked, i, j);
    }

    navigateToApplication() {
        if (this.numSelected.getValue() === 0) {
            this.modalHeader.next("modal-header-danger");
            this.modalTitle.next("Επιλογή αριθμού σχολείων");
            if (this.numSelected.getValue() === 0)
                this.modalText.next("Δεν έχετε επιλέξει κανένα σχολείο!");

            this.showModal();
        }
        else
            this.router.navigate(["/schools-order-select"]);

    }
}
