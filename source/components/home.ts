import { NgRedux } from "@angular-redux/store";
import { Component, OnDestroy, OnInit } from "@angular/core";
import { ActivatedRoute, Params, Router } from "@angular/router";
import { CookieService } from "ngx-cookie";
import { BehaviorSubject, Subscription } from "rxjs/Rx";

import { LoginInfoActions } from "../actions/logininfo.actions";
import { API_ENDPOINT, API_ENDPOINT_PARAMS } from "../app.settings";
import { STUDENT_ROLE } from "../constants";
import { HelperDataService } from "../services/helper-data-service";
import { LOGININFO_INITIAL_STATE } from "../store/logininfo/logininfo.initial-state";
import { ILoginInfoRecords } from "../store/logininfo/logininfo.types";
import { IAppState } from "../store/store";

import { StudentDataFieldsActions } from "../actions/studentdatafields.actions";
import { RegionSchoolsActions } from "../actions/regionschools.actions";
import { SectorCoursesActions } from "../actions/sectorcourses.actions";
import { SectorFieldsActions } from "../actions/sectorfields.actions";
import { DataModeActions } from "../actions/datamode.actions";
import { EpalClassesActions } from "../actions/epalclass.actions";

import { SchoolTypeActions } from "../actions/schooltype.actions";
import { GelClassesActions } from "../actions/gelclasses.actions";
import { ElectiveCourseFieldsActions } from "../actions/electivecoursesfields.actions";
import { OrientationGroupActions } from "../actions/orientationgroup.action";
import { LangCourseFieldsActions } from "../actions/langcoursesfields.actions";
import { GelStudentDataFieldsActions } from "../actions/gelstudentdatafields.actions";


@Component({
    selector: "home",
    template: `
  <div>
       <form  method = "POST" action="{{apiEndPoint}}/oauth/login{{apiEndPointParams}}" #form>
       <!--
       <form  method = "POST" action="https://eduslim2.minedu.gov.gr/drupal//oauth/login{{apiEndPointParams}}" #form>
       -->

            <!--
            <div class="bg-warning" style="padding: 2em;">

            <p>
            <strong>Ανακοίνωση:</strong> Σας ενημερώνουμε οτι το σύστημα θα κλείσει οριστικά
                <ul>
                <li> Την Τρίτη 12/9/2017 στις 8:30
                </li>

                </ul>
            </div>
            -->

            <div> <p></p><p></p></div>


            <div *ngFor="let loginInfoToken$ of loginInfo$ | async; let i=index"></div>
            <div class="row" style="min-height: 300px; margin-top: 100px;">

            <div *ngIf="!authToken" class="col-md-8 offset-md-4">
                <button type="submit" class="btn-primary btn-lg" (click)="form.submit()">
                Είσοδος μέσω TaxisNet
                </button>
            </div>
            </div>

     </form>
  </div>
  `
})

export default class Home implements OnInit, OnDestroy {
    private authToken: string;
    private authRole: string;
    private name: any;
    private xcsrftoken: any;
    private loginInfo$: BehaviorSubject<ILoginInfoRecords>;
    private apiEndPoint = API_ENDPOINT;
    private apiEndPointParams = API_ENDPOINT_PARAMS;
    private loginInfoSub: Subscription;

    constructor(
        private _ata: LoginInfoActions,
        private _cfc: EpalClassesActions,
        private _cfa: DataModeActions,
        private _sdfa: StudentDataFieldsActions,
        private _csa: SectorCoursesActions,
        private _sfa: SectorFieldsActions,
        private _rsa: RegionSchoolsActions,
        private _sta: SchoolTypeActions,
        private _gca: GelClassesActions,
        private _ecfa: ElectiveCourseFieldsActions,
        private _oga: OrientationGroupActions,
        private _lcfa: LangCourseFieldsActions,
        private _gsdfa: GelStudentDataFieldsActions,
        private _ngRedux: NgRedux<IAppState>,
        private activatedRoute: ActivatedRoute,
        private _hds: HelperDataService,
        private router: Router,
        private _cookieService: CookieService
    ) {
        this.authToken = "";
        this.authRole = "";
        this.name = "";
        this.loginInfo$ = new BehaviorSubject(LOGININFO_INITIAL_STATE);
    };
    ngOnInit() {
        this.loginInfoSub = this._ngRedux.select("loginInfo")
            .map(loginInfo => <ILoginInfoRecords>loginInfo)
            .subscribe(linfo => {
            if (linfo.size > 0) {
                linfo.reduce(({}, loginInfoObj) => {
                    this.authToken = loginInfoObj.auth_token;
                    this.authRole = loginInfoObj.auth_role;
                    if (this.authToken && this.authToken.length > 0 && this.authRole && this.authRole === STUDENT_ROLE) {
                        if (loginInfoObj.lock_application_epal === 1 && loginInfoObj.lock_application_gel === 1)
                            this.router.navigate(["/info"]);
                        else {
                            this._cfc.initEpalClasses();
                            this._cfa.initDataMode();
                            this._sdfa.initStudentDataFields();
                            this._sfa.initSectorFields();
                            this._rsa.initRegionSchools();
                            this._csa.initSectorCourses();

                            this._sta.initSchoolType();
                            this._gca.initGelClasses();
                            this._ecfa.initElectiveCourseFields();
                            this._oga.initOrientationGroup();
                            this._lcfa.initLangCourseFields();
                            this._gsdfa.initGelStudentDataFields();

                            this.router.navigate(["/parent-form"]);
                        }
                    }
                    return loginInfoObj;
                }, {});
            }

            this.loginInfo$.next(linfo);
        }, error => { console.log("error selecting loginInfo"); });

        // subscribe to router event
        this.activatedRoute.queryParams.subscribe((params: Params) => {
            if (params) {
                this.authToken = params["auth_token"];
                this.authRole = params["auth_role"];
            }

            if (this.authToken && this.authRole)
                this._ata.getloginInfo({ auth_token: this.authToken, auth_role: this.authRole });

        });
    }

    ngOnDestroy() {
        if (this.loginInfoSub) this.loginInfoSub.unsubscribe();
    }

    getCookie(key: string) {
        return this._cookieService.get(key);
    }

    removeCookie(key: string) {
        return this._cookieService.remove(key);
    }

    checkvalidation() {

    }
}
