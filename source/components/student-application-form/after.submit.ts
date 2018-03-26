import { NgRedux } from "@angular-redux/store";
import { Component, Injectable, OnDestroy, OnInit } from "@angular/core";
import { Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";

import { EpalClassesActions } from "../../actions/epalclass.actions";
import { LoginInfoActions } from "../../actions/logininfo.actions";
import { RegionSchoolsActions } from "../../actions/regionschools.actions";
import { SectorCoursesActions } from "../../actions/sectorcourses.actions";
import { SectorFieldsActions } from "../../actions/sectorfields.actions";
import { StudentDataFieldsActions } from "../../actions/studentdatafields.actions";
import { SchoolTypeActions } from "../../actions/schooltype.actions";
import { GelClassesActions } from "../../actions/gelclasses.actions";
import { ElectiveCourseFieldsActions } from "../../actions/electivecoursesfields.actions";
import { OrientationGroupActions } from "../../actions/orientationgroup.action";
import { LangCourseFieldsActions } from "../../actions/langcoursesfields.actions";
import { GelStudentDataFieldsActions } from "../../actions/gelstudentdatafields.actions";
import { HelperDataService } from "../../services/helper-data-service";
import { ILoginInfoRecords } from "../../store/logininfo/logininfo.types";
import { IAppState } from "../../store/store";

@Component({
    selector: "post-submit",
    template: `
        <div class = "loading" *ngIf="(showLoader$ | async) === true"></div>
           <div class="row" style="margin-top: 130px; margin-bottom: 200px;">
               <div class="col-md-3 offset-md-3">
                <button type="submit" class="btn-primary btn-lg btn-block isclickable" style="margin: 0px; font-size: 1em; padding: 5px; height: 8em;" (click)="submittedView()">
                Εμφάνιση<br />Εκτύπωση<br />Δήλωσης<br />Προτίμησης
                </button>
                </div>
                <div class="col-md-6">
                 <button type="submit" class="btn-primary btn-lg btn-block isclickable" style="margin: 0px; font-size: 1em; padding: 5px; height: 8em;" (click)="signOut()">
                Αποσύνδεση
                </button>
               </div>
            </div>
  `
})

@Injectable() export default class AfterSubmit implements OnInit, OnDestroy {
    private authToken: string;
    private authRole: string;
    private cuName: string;
    private cuser: any;
    private showLoader$: BehaviorSubject<boolean>;
    private loginInfoSub: Subscription;

    constructor(
        private _ata: LoginInfoActions,
        private _hds: HelperDataService,
        private _csa: SectorCoursesActions,
        private _sfa: SectorFieldsActions,
        private _rsa: RegionSchoolsActions,
        private _eca: EpalClassesActions,
        private _sdfa: StudentDataFieldsActions,
        private _sta: SchoolTypeActions,
        private _gca: GelClassesActions,
        private _ecfa: ElectiveCourseFieldsActions,
        private _oga: OrientationGroupActions,
        private _lcfa: LangCourseFieldsActions,
        private _gsdfa: GelStudentDataFieldsActions,
        private _ngRedux: NgRedux<IAppState>,
        private router: Router
    ) {
        this.showLoader$ = new BehaviorSubject(false);
    };

    ngOnInit() {
        this.loginInfoSub = this._ngRedux.select("loginInfo")
            .map(loginInfo => <ILoginInfoRecords>loginInfo)
            .subscribe(linfo => {
                if (linfo.size > 0) {
                    linfo.reduce(({}, loginInfoObj) => {
                        this.authToken = loginInfoObj.auth_token;
                        this.authRole = loginInfoObj.auth_role;
                        this.cuName = loginInfoObj.cu_name;
                        return loginInfoObj;
                    }, {});
                }
            }, error => { console.log("error selecting loginInfo"); });
    };

    ngOnDestroy() {
        if (this.loginInfoSub)
            this.loginInfoSub.unsubscribe();
    };

    signOut() {
        this.showLoader$.next(true);
        this._hds.signOut().then(data => {
            this._ata.initLoginInfo();
            this._eca.initEpalClasses();
            this._sfa.initSectorFields();
            this._rsa.initRegionSchools();
            this._csa.initSectorCourses();
            this._sdfa.initStudentDataFields();

            this._sta.initSchoolType();
            this._gca.initGelClasses();
            this._ecfa.initElectiveCourseFields();
            this._oga.initOrientationGroup();
            this._lcfa.initLangCourseFields();
            this._gsdfa.initGelStudentDataFields();

            this.router.navigate([""]);
            this.authToken = "";
            this.authRole = "";
            this.showLoader$.next(false);
        }).catch(err => {
            this.showLoader$.next(false);
            console.log(err);
        });

    }

    submittedView() {
        this.router.navigate(["/submited-preview"]);
    }

}
