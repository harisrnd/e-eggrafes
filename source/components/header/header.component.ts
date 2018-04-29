import { NgRedux } from "@angular-redux/store";
import { Component, OnDestroy, OnInit } from "@angular/core";
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

import { DIDE_ROLE, MINISTRY_ROLE, PDE_ROLE, SCHOOL_ROLE, STUDENT_ROLE, SCHOOLGEL_ROLE, SCHOOLGYM_ROLE } from "../../constants";
import { HelperDataService } from "../../services/helper-data-service";
import { LOGININFO_INITIAL_STATE } from "../../store/logininfo/logininfo.initial-state";
import { ILoginInfoRecords } from "../../store/logininfo/logininfo.types";
import { IAppState } from "../../store/store";

@Component({
    selector: "reg-header",
    templateUrl: "header.component.html"
})
export default class HeaderComponent implements OnInit, OnDestroy {
    private authToken: string;
    private studentRole = STUDENT_ROLE;
    private authRole: string;
    private cuName: string;
    private loginInfo$: BehaviorSubject<ILoginInfoRecords>;
    private cuser: any;
    private showLoader$: BehaviorSubject<boolean>;
    private modalTitle: BehaviorSubject<string>;
    private modalText: BehaviorSubject<string>;
    private modalHeader: BehaviorSubject<string>;

    private TotalStudents$: BehaviorSubject<any>;
    private TotalStudentsSub: Subscription;
    private showLoader: BehaviorSubject<boolean>;
    private hasvalue: boolean;
    private loginInfoSub: Subscription;

    constructor(private _ata: LoginInfoActions,
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

        this.authToken = "";
        this.authRole = "";
        this.cuName = "";
        this.loginInfo$ = new BehaviorSubject(LOGININFO_INITIAL_STATE);
        this.showLoader$ = new BehaviorSubject(false);
        this.modalTitle = new BehaviorSubject("");
        this.modalText = new BehaviorSubject("");
        this.modalHeader = new BehaviorSubject("");
        this.TotalStudents$ = new BehaviorSubject([{}]);
        this.showLoader = new BehaviorSubject(false);
        this.hasvalue = false;

    };

    ngOnInit() {
        (<any>$("#headerNotice")).appendTo("body");
        this.loginInfoSub = this._ngRedux.select("loginInfo")
            .map(loginInfo => <ILoginInfoRecords>loginInfo)
            .subscribe(loginInfo => {
                if (loginInfo.size > 0) {
                    loginInfo.reduce(({}, loginInfoObj) => {
                        this.authToken = loginInfoObj.auth_token;
                        this.authRole = loginInfoObj.auth_role;
                        this.cuName = loginInfoObj.cu_name;
                        return loginInfoObj;
                    }, {});

                    if (this.hasvalue === false && this.authRole === STUDENT_ROLE) {
                        this.showLoader.next(true);
                        this.TotalStudentsSub = this._hds.findTotalStudents().subscribe(x => {
                            this.TotalStudents$.next(x);
                            this.showLoader.next(false);
                            this.hasvalue = true;
                        },
                            error => {
                                this.TotalStudents$.next([{}]);
                                console.log("Error Getting courses perSchool");
                                this.showLoader.next(false);
                            });
                    }
                }
                this.loginInfo$.next(loginInfo);
            });

    }

    ngOnDestroy() {
        (<any>$("#headerNotice")).remove();
        if (this.loginInfoSub) {
            this.loginInfoSub.unsubscribe();
        }
    }

    signOut() {
        this.showLoader$.next(true);
        this._hds.signOut().then(data => {
            this._ata.initLoginInfo();
            if (this.authRole === SCHOOL_ROLE || this.authRole === SCHOOLGEL_ROLE || this.authRole === SCHOOLGYM_ROLE) {
                this.authToken = "";
                this.authRole = "";
                window.location.assign((<any>data).next);
            }
            else if (this.authRole === PDE_ROLE) {
                this.authToken = "";
                this.authRole = "";
                window.location.assign((<any>data).next);
            }
            else if (this.authRole === DIDE_ROLE) {
                this.authToken = "";
                this.authRole = "";
                window.location.assign((<any>data).next);
            }
            else if (this.authRole === STUDENT_ROLE) {
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
            }
            else if (this.authRole === MINISTRY_ROLE) {
                this.router.navigate(["/ministry"]);
            }
            this.authToken = "";
            this.authRole = "";
            this.showLoader$.next(false);
        }).catch(err => {
            this.showLoader$.next(false);
            console.log(err);
        });
    }

    goHome() {
        if (this.authRole === SCHOOL_ROLE || this.authRole === SCHOOLGEL_ROLE || this.authRole === SCHOOLGYM_ROLE) {
            this.router.navigate(["/school"]);
        }
        else if (this.authRole === PDE_ROLE) {
            this.router.navigate(["/school"]);
        }
        else if (this.authRole === DIDE_ROLE) {
            this.router.navigate(["/school"]);
        }
        else if (this.authRole === STUDENT_ROLE) {
            this.router.navigate([""]);
        }
        else if (this.authRole === MINISTRY_ROLE) {
            this.router.navigate(["/ministry"]);
        }
    }

    gohelpDesk() {
        this.router.navigate(["/help-desk"]);
    }

    public showModal(): void {
        (<any>$("#headerNotice")).modal("show");
    }

    public hideModal(): void {
        (<any>$("#headerNotice")).modal("hide");
    }

}
