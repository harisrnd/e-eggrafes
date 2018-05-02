import { NgRedux } from "@angular-redux/store";
import { Component, OnDestroy, OnInit } from "@angular/core";
import { Injectable } from "@angular/core";
import { FormBuilder, FormGroup } from "@angular/forms";
import { Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";

import { SchoolTypeActions } from "../../actions/schooltype.actions";
import { GelClassesActions } from "../../actions/gelclasses.actions";
import { OrientationGroupActions } from "../../actions/orientationgroup.action";
import { ElectiveCourseFieldsActions } from "../../actions/electivecoursesfields.actions";
import { LangCourseFieldsActions } from "../../actions/langcoursesfields.actions";
import { EpalClassesActions } from "../../actions/epalclass.actions";
import { RegionSchoolsActions } from "../../actions/regionschools.actions";
import { SectorCoursesActions } from "../../actions/sectorcourses.actions";
import { SectorFieldsActions } from "../../actions/sectorfields.actions";
import { LoginInfoActions } from "../../actions/logininfo.actions";

import { SCHOOLTYPE_INITIAL_STATE } from "../../store/schooltype/schooltype.initial-state";
import { ISchoolType, ISchoolTypeRecord, ISchoolTypeRecords } from "../../store/schooltype/schooltype.types";
import { ILoginInfoRecords } from "../../store/logininfo/logininfo.types";
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
    <p style="margin-top: 5px; line-height: 2em;"> Παρακαλώ επιλέξτε τον τύπο σχολείου που θα φοιτήσει ο μαθητής
            κατά το σχολικό έτος 2018-19,  πατώντας Γενικό Λύκειο (ΓΕΛ) ή Επαγγελματικό Λύκειο (ΕΠΑΛ)</p>
        <!-- <div class="form-group" style= "margin-top: 50px; margin-bottom: 100px;">
            <label for="typeId">Τύπος Σχολείου:</label><br/>
            <select class="form-control" formControlName="typeId" (change)="initializestore()">
              <option value="0">Επιλέξτε Τύπο Σχολείου</option>
              <option value="1">ΓΕΛ - Γενικό Λύκειο</option>
              <option value="2">ΕΠΑΛ - Επαγγελματικό Λύκειο</option>
            </select>
        </div> -->

        <div class="row" style="margin-top: 60px; margin-bottom: 80px;">
       
        <div class="col-md-1 ">
        </div>
        <div class="col-md-5 ">
           <button class="button  isclickable pull-center" (click)="GelSelected()"><img class="isclickable pull-center" src="../theme/assets/images/GEL.png" alt=""></button>
        </div>
        <div class="col-md-5 ">
            <button class="button isclickable pull-center" (click)="EpalSelected()"><img class = "isclickable pull-center" src="../theme/assets/images/LOGO epal.png" alt=""></button>
        </div>
        <div class="col-md-1 ">
        </div>

        </div>

        <!--
        <div class="row" style="margin-top: 20px; margin-bottom: 20px;">
            <div class="col-md-6">
                <button type="button" class="btn-primary btn-lg pull-left" (click)="navigateBack()">
                    <i class="fa fa-backward"></i>
                </button>
            </div>
            <div class="col-md-6">
                <button type="button" class="btn-primary btn-lg pull-right isclickable" style="width: 9em;" (click)="Selected()">
               <span style="font-size: 0.9em; font-weight: bold;">Συνέχεια&nbsp;&nbsp;&nbsp;</span><i class="fa fa-forward"></i>
                </button>
            </div>
        </div>-->

    </form>
   `
})

@Injectable() export default class SchoolTypeSelection implements OnInit, OnDestroy {
    private schooltype$: BehaviorSubject<ISchoolTypeRecords>;
    private schooltypeSub: Subscription;
    private loginInfoSub: Subscription;
    private formGroup: FormGroup;
    private modalTitle: BehaviorSubject<string>;
    private modalText: BehaviorSubject<string>;
    private modalHeader: BehaviorSubject<string>;
    public isModalShown: BehaviorSubject<boolean>;
    private lock_application_epal: BehaviorSubject<number>;
    private lock_application_gel: BehaviorSubject<number>;

    constructor(private fb: FormBuilder,
        private _ngRedux: NgRedux<IAppState>,
        private _lia: LoginInfoActions,
        private _sta: SchoolTypeActions,
        private _gca: GelClassesActions,
        private _ogs: OrientationGroupActions,
        private _cfe: ElectiveCourseFieldsActions,
        private _lcfa: LangCourseFieldsActions,
        private _eca: EpalClassesActions,
        private _sca: SectorCoursesActions,
        private _sfa: SectorFieldsActions,
        private _rsa: RegionSchoolsActions,
        private router: Router) {
        this.formGroup = this.fb.group({
            typeId: []
        });
        this.modalTitle = new BehaviorSubject("");
        this.modalText = new BehaviorSubject("");
        this.modalHeader = new BehaviorSubject("");
        this.isModalShown = new BehaviorSubject(false);
        this.lock_application_epal = new BehaviorSubject(1);
        this.lock_application_gel = new BehaviorSubject(1);
        this.schooltype$ = new BehaviorSubject(SCHOOLTYPE_INITIAL_STATE);
    };

    ngOnInit() {

        this._sta.initSchoolType();
        this._eca.initEpalClasses();
        this._sfa.initSectorFields();
        this._rsa.initRegionSchools();
        this._sca.initSectorCourses();
        this._gca.initGelClasses();
        this._ogs.initOrientationGroup();
        this._cfe.initElectiveCourseFields();
        this._lcfa.initLangCourseFields();

        (<any>$("#SchoolTypeNotice")).appendTo("body");

        this.loginInfoSub = this._ngRedux.select("loginInfo")
            .map(loginInfo => <ILoginInfoRecords>loginInfo)
            .subscribe(linfo => {
                if (linfo.size > 0) {
                    linfo.reduce(({}, loginInfoObj) => {
                        this.lock_application_epal.next(loginInfoObj.lock_application_epal);
                        this.lock_application_gel.next(loginInfoObj.lock_application_gel);
                        return loginInfoObj;
                    }, {});
                }
            }, error => { console.log("error selecting loginInfo"); });

/*         this.schooltypeSub = this._ngRedux.select("schooltype")
            .map(schooltype => <ISchoolTypeRecords>schooltype)
            .subscribe(ecs => {
                if (ecs.size > 0) {
                      ecs.reduce(({}, type) => {
                        //this.formGroup.controls["typeId"].setValue(type.get("id"));
                        return type;
                    }, {});
                } else {
                    //this.formGroup.controls["typeId"].setValue("0");
                }
                this.schooltype$.next(ecs);
            }, error => { console.log("error selecting schooltype"); }); */

    }

    ngOnDestroy() {
        if (this.schooltypeSub)
            this.schooltypeSub.unsubscribe();
        if (this.loginInfoSub)
            this.loginInfoSub.unsubscribe();
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


/*     saveSelected() {
        if (this.formGroup.controls["typeId"].value === "0") {
            this.modalTitle.next("Δεν επιλέχθηκε τύπος Σχολείου");
            this.modalText.next("Παρακαλούμε να επιλέξετε πρώτα τον τύπο Σχολείου φοίτησης του μαθητή για το νέο σχολικό έτος");
            this.modalHeader.next("modal-header-danger");
            this.showModal();
        }
        else {
            if (this.formGroup.value.typeId === "1"){
                this._sta.saveSchoolTypeSelected(this.formGroup.value.typeId,"ΓΕΛ");
                this.router.navigate(["/gel-class-select"]);
            }
            else if (this.formGroup.value.typeId === "2"){
                this._sta.saveSchoolTypeSelected(this.formGroup.value.typeId,"ΕΠΑΛ");
                this.router.navigate(["/epal-class-select"]);
            }
        }

    }

    initializestore() {
        this._eca.initEpalClasses();
        this._sfa.initSectorFields();
        this._rsa.initRegionSchools();
        this._sca.initSectorCourses();
        this._gca.initGelClasses();
        this._ogs.initOrientationGroup();
        this._cfe.initElectiveCourseFields();
        if (this.formGroup.value.typeId === "1"){
            this._sta.saveSchoolTypeSelected(this.formGroup.value.typeId,"ΓΕΛ");
        }
        else if (this.formGroup.value.typeId === "2"){
            this._sta.saveSchoolTypeSelected(this.formGroup.value.typeId,"ΕΠΑΛ");
        }

    } */

    GelSelected() {
        if (this.lock_application_gel.getValue() === 1)
            this.router.navigate(["/info"]);
        else {
          this._sta.saveSchoolTypeSelected(1,"ΓΕΛ");
          this.router.navigate(["/gel-class-select"]);
        }
    }

    EpalSelected() {
      if (this.lock_application_epal.getValue() === 1)
          this.router.navigate(["/info"]);
      else {
        this._sta.saveSchoolTypeSelected(2,"ΕΠΑΛ");
        this.router.navigate(["/epal-class-select"]);
      }
    }

}
