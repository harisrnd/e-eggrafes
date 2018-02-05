import { NgRedux } from "@angular-redux/store";
import { Component, OnDestroy, OnInit } from "@angular/core";
import { Injectable } from "@angular/core";
import { FormBuilder, FormGroup } from "@angular/forms";
import { Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";

import { GelClassesActions } from "../../actions/gelclasses.actions";
import { OrientationGroupActions } from "../../actions/orientationgroup.action";
import { ElectiveCourseFieldsActions } from "../../actions/electivecoursesfields.actions";
import { GELCLASSES_INITIAL_STATE } from "../../store/gelclasses/gelclasses.initial-state";
import { IGelClass, IGelClassRecord, IGelClassRecords } from "../../store/gelclasses/gelclasses.types";
import { IAppState } from "../../store/store";
import { gelclassesReducer } from "../../store/gelclasses/gelclasses.reducer";

@Component({
    selector: "gel-class-select",
    template: `
    <div id="gelClassNotice" (onHidden)="onHidden()" class="modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header {{modalHeader | async}}">
              <h3 class="modal-title pull-left"><i class="fa fa-check-square-o"></i>&nbsp;&nbsp;{{ modalTitle | async }}</h3>
            <button type="button" class="close pull-right" aria-label="Close" (click)="hideModal()">
              <span aria-hidden="true"><i class="fa fa-times"></i></span>
            </button>
          </div>
          <div class="modal-body">
              <p>{{ modalText | async }}</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal" (click)="hideModal()">Κλείσιμο</button>
          </div>
        </div>
      </div>
    </div>

    <h4> Επιλογή Τάξης </h4>
    <form [formGroup]="formGroup">
    <div class = "loading" *ngIf="(gelclasses$ | async).size === 0">
    </div>
    <p style="margin-top: 5px; line-height: 2em;"> Παρακαλώ καθορίστε την κατηγορία ΓΕΛ που θα φοιτήσει ο μαθητής
            κατά το σχολικό έτος 2018-19, επιλέγοντας ΗΜΕΡΗΣΙΟ ή ΕΣΠΕΡΙΝΟ.</p>
        <div style= "margin-top: 50px; margin-bottom: 100px;">
            <label for="category">Κατηγορία ΓΕ.Λ.</label><br/>
            <select class="form-group" #type_sel class="form-control" formControlName="category" (change)="categoryselected(type_sel)">
                <option value="0">Επιλέξτε Τύπο ΓΕΛ</option>
                <option value="ΗΜΕΡΗΣΙΟ">ΗΜΕΡΗΣΙΟ</option>
                <option value="ΕΣΠΕΡΙΝΟ">ΕΣΠΕΡΙΝΟ</option>
            </select>
        </div>
    <p *ngIf = "enableclassfilter" style="margin-top: 5px; line-height: 2em;"> Παρακαλώ επιλέξτε την τάξη φοίτησης του μαθητή
            στo Γενικό Λύκειο κατά το σχολικό έτος 2018-19 και έπειτα επιλέξτε <i>Συνέχεια</i>.</p>
        <div class="form-group" style= "margin-top: 50px; margin-bottom: 100px;">
            <label for="classId" *ngIf = "enableclassfilter">Τάξη:</label><br/>
            <select class="form-control" *ngIf = "enableclassfilter" formControlName="classId" (change)="initializestore()" >
            <option value="0">Επιλέξτε Τάξη</option>
            <option *ngFor="let gelclass$ of gelclasses$ | async;" [value] = "gelclass$.id" [hidden] = "gelclass$.category != categoryChosen" > {{gelclass$.name}} - {{gelclass$.category}}</option>
            </select>
        </div>

        <div class="row" style="margin-top: 20px; margin-bottom: 20px;" *ngIf="(gelclasses$ | async).size > 0">
            <div class="col-md-6">
                <button type="button" class="btn-primary btn-lg pull-left" (click)="navigateBack()">
                    <i class="fa fa-backward"></i>
                </button>
            </div>
            <div class="col-md-6">
                <button type="button" class="btn-primary btn-lg pull-right isclickable" style="width: 9em;" (click)="saveSelected()">
               <span style="font-size: 0.9em; font-weight: bold;">Συνέχεια&nbsp;&nbsp;&nbsp;</span><i class="fa fa-forward"></i>
                </button>
            </div>
        </div>

    </form>
   `
})

@Injectable() export default class ClassSelection implements OnInit, OnDestroy {

    private gelclasses$: BehaviorSubject<IGelClassRecords>;
    private gelclassesSub: Subscription;
    private categoryChosen: String;
    private enableclassfilter: boolean;
    private classActive;
    private formGroup: FormGroup;
    private modalTitle: BehaviorSubject<string>;
    private modalText: BehaviorSubject<string>;
    private modalHeader: BehaviorSubject<string>;
    public isModalShown: BehaviorSubject<boolean>;

    constructor(private fb: FormBuilder,
        private _ngRedux: NgRedux<IAppState>,
        private _cfa: GelClassesActions,
        private _ogs: OrientationGroupActions,
        private _cfe: ElectiveCourseFieldsActions,


        private router: Router) {
        this.formGroup = this.fb.group({
            classId: [],
            category: []
        });
        this.modalTitle = new BehaviorSubject("");
        this.modalText = new BehaviorSubject("");
        this.modalHeader = new BehaviorSubject("");
        this.isModalShown = new BehaviorSubject(false);
        this.gelclasses$ = new BehaviorSubject(GELCLASSES_INITIAL_STATE);
        this.enableclassfilter = false;
        this.classActive=0;
    };

    ngOnInit() {
        (<any>$("#gelClassNotice")).appendTo("body");

        this._cfa.getClassesList(false);
        this.gelclassesSub = this._ngRedux.select("gelclasses")
            .map(gelclasses => <IGelClassRecords>gelclasses)
            .subscribe(ecs => {
                if (ecs.size > 0) {
                     ecs.reduce(({}, gelclass) => {
                        if (gelclass.get("selected")===true ){
                            this.formGroup.controls["classId"].setValue(gelclass.get("id"));
                            this.formGroup.controls["category"].setValue(gelclass.get("category"));
                            this.enableclassfilter = true;
                            this.classActive=gelclass.get("id");
                            this.categoryChosen=gelclass.get("category");
                        }
                        return gelclass;
                    }, {});
                } else {
                    //this.formGroup.controls["classId"].setValue("...");
                }
                if (this.enableclassfilter === false){
                    this.formGroup.controls["category"].setValue("0");
                }
                this.gelclasses$.next(ecs);
            }, error => { console.log("error selecting gelclasses"); });

    }

    ngOnDestroy() {
        if (this.gelclassesSub)
            this.gelclassesSub.unsubscribe();
        (<any>$("#gelClassNotice")).remove();
    }

    public showModal(): void {
        (<any>$("#gelClassNotice")).modal("show");
    }

    public hideModal(): void {
        (<any>$("#gelClassNotice")).modal("hide");

    }

    public onHidden(): void {
        this.isModalShown.next(false);
    }

    navigateBack() {
        this.router.navigate(["/school-type-select"]);
    }

    public categoryselected(typeId) {

        this.categoryChosen = typeId.value;
        this._cfa.resetGelClassesSelected();

        if (this.categoryChosen == "ΗΜΕΡΗΣΙΟ" || this.categoryChosen == "ΕΣΠΕΡΙΝΟ") {
            this.enableclassfilter=true;
            this.formGroup.controls["classId"].setValue("0");
        }
        else{
            this.enableclassfilter=false;
        } // end if
    }


    saveSelected() {
        if (this.formGroup.controls["classId"].value === "0" || this.enableclassfilter===false) {
            this.modalTitle.next("Δεν επιλέχθηκε τάξη");
            this.modalText.next("Παρακαλούμε να επιλέξετε πρώτα τάξη φοίτησης του μαθητή για το νέο σχολικό έτος");
            this.modalHeader.next("modal-header-danger");
            this.showModal();
        }
        else {
            this._cfa.saveGelClassesSelected(this.classActive-1, this.formGroup.value.classId-1);
            //Όταν class_id = 3 (Γ' Λυκείου - Ημερήσιο), τότε πήγαινε πρώτα στη σελίδα επιλογής για επιλογή προσανατολισμού
            //και μετά στην επιλογή για μάθημα επιλογής
            if (this.formGroup.value.classId === "2" || this.formGroup.value.classId === "3" || this.formGroup.value.classId === "6" || this.formGroup.value.classId === "7")
              this.router.navigate(["/orientation-group-select"]);
            else if (this.formGroup.value.classId === "1" || this.formGroup.value.classId === "4")
              this.router.navigate(["/electivecourse-fields-select"]);
            else if (this.formGroup.value.classId === "5")
              this.router.navigate(["/gelstudent-application-form-main"]);
        }

    }

    initializestore() {

        this._cfa.saveGelClassesSelected(this.classActive-1, this.formGroup.value.classId-1);

        this.classActive=this.formGroup.value.classId;

        if (this.classActive == 2 || this.classActive == 3 || this.classActive == 6 || this.classActive == 7 )
          this._ogs.initOrientationGroup();
        if (this.classActive == 1 || this.classActive == 3 || this.classActive == 4 )
          this._cfe.initElectiveCourseFields();

    }

}
