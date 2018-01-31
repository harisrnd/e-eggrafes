import { NgRedux } from "@angular-redux/store";
import { Component, OnDestroy, OnInit } from "@angular/core";
import { Injectable } from "@angular/core";
import { FormBuilder, FormGroup } from "@angular/forms";
import { Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";

import { SchoolTypeActions } from "../../actions/schooltype.actions";
import { GelClassesActions } from "../../actions/gelclasses.actions";

import { SCHOOLTYPE_INITIAL_STATE } from "../../store/schooltype/schooltype.initial-state";
import { ISchoolType, ISchoolTypeRecord, ISchoolTypeRecords } from "../../store/schooltype/schooltype.types";
import { IAppState } from "../../store/store";
import { schooltypeReducer } from "../../store/schooltype/schooltype.reducer";

@Component({
    selector: "school-type-select", 
    template: `
    <div id="SchoolTypeNotice" (onHidden)="onHidden()" class="modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
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

    <h4> Επιλογή Τύπου Σχολείου </h4>
    <form [formGroup]="formGroup">
    <p style="margin-top: 5px; line-height: 2em;"> Παρακαλώ καθορίστε τον τύπο σχολείου που θα φοιτήσει ο μαθητής
            κατά το σχολικό έτος 2018-19,  επιλέγοντας Γενικό Λύκειο (ΓΕΛ) ή Επαγγελματικό Λύκειο (ΕΠΑΛ), και έπειτα πατήστε <i>Συνέχεια</i>.</p>
        <div class="form-group" style= "margin-top: 50px; margin-bottom: 100px;">
            <label for="typeId">Τύπος Σχολείου:</label><br/>
            <select class="form-control" formControlName="typeId" (change)="initializestore()">
            <option value="0">Επιλέξτε Τύπο Σχολείου:</option>
            <option value="1">ΓΕΛ - Γενικό Λύκειο</option>
            <option value="2">ΕΠΑΛ - Επαγγελματικό Λύκειο</option>
            </select>
        </div>

        <div class="row" style="margin-top: 20px; margin-bottom: 20px;">
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

@Injectable() export default class SchoolTypeSelection implements OnInit, OnDestroy {
    private schooltype$: BehaviorSubject<ISchoolTypeRecords>;
    private schooltypeSub: Subscription;

    private formGroup: FormGroup;
    private modalTitle: BehaviorSubject<string>;
    private modalText: BehaviorSubject<string>;
    private modalHeader: BehaviorSubject<string>;
    public isModalShown: BehaviorSubject<boolean>;



    constructor(private fb: FormBuilder,
        private _ngRedux: NgRedux<IAppState>,
        private _cfa: SchoolTypeActions,
        private _gca: GelClassesActions,
        private router: Router) {
        this.formGroup = this.fb.group({
            typeId: []
        });
        this.modalTitle = new BehaviorSubject("");
        this.modalText = new BehaviorSubject("");
        this.modalHeader = new BehaviorSubject("");
        this.isModalShown = new BehaviorSubject(false);
        this.schooltype$ = new BehaviorSubject(SCHOOLTYPE_INITIAL_STATE);
    };

    ngOnInit() {
        (<any>$("#SchoolTypeNotice")).appendTo("body");

        this.schooltypeSub = this._ngRedux.select("schooltype")
            .map(schooltype => <ISchoolTypeRecords>schooltype)
            .subscribe(ecs => {
                if (ecs.size > 0) {
                      ecs.reduce(({}, type) => {

                        this.formGroup.controls["typeId"].setValue(type.get("id"));
                        return type;
                    }, {});
                } else {
                    this.formGroup.controls["typeId"].setValue("0");
                }
                this.schooltype$.next(ecs);
            }, error => { console.log("error selecting schooltype"); });

    }

    ngOnDestroy() {
        if (this.schooltypeSub)
            this.schooltypeSub.unsubscribe();
        (<any>$("#SchoolTypeNotice")).remove();
    }

    public showModal(): void {
        (<any>$("#SchoolTypeNotice")).modal("show");
    }

    public hideModal(): void {
        (<any>$("#SchoolTypeNotice")).modal("hide");

    }

    public onHidden(): void {
        this.isModalShown.next(false);
    }

    navigateBack() {
        this.router.navigate(["/school-type-select"]);
    }


    saveSelected() {
        if (this.formGroup.controls["typeId"].value === "0") {
            this.modalTitle.next("Δεν επιλέχθηκε τύπος Σχολείου");
            this.modalText.next("Παρακαλούμε να επιλέξετε πρώτα τον τύπο Σχολείου φοίτησης του μαθητή για το νέο σχολικό έτος");
            this.modalHeader.next("modal-header-danger");
            this.showModal();
        }
        else {
            if (this.formGroup.value.typeId === "1"){
                this._cfa.saveSchoolTypeSelected(this.formGroup.value.typeId,"ΓΕΛ");
                this.router.navigate(["/gel-class-select"]);
            }
            else if (this.formGroup.value.typeId === "2"){
                this._cfa.saveSchoolTypeSelected(this.formGroup.value.typeId,"ΕΠΑΛ");
                this.router.navigate(["/epal-class-select"]);
            }
        }

    }

    initializestore() {
        this._gca.initGelClasses();
    }

}
