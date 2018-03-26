import { NgRedux } from "@angular-redux/store";
import { Component, OnDestroy, OnInit } from "@angular/core";
import { Injectable } from "@angular/core";
import { BehaviorSubject, Subscription } from "rxjs/Rx";

import { LOGININFO_INITIAL_STATE } from "../../store/logininfo/logininfo.initial-state";
import { ILoginInfoRecords } from "../../store/logininfo/logininfo.types";
import { IAppState } from "../../store/store";
import { GelClassesActions } from "../../actions/gelclasses.actions";
import { OrientationGroupActions } from "../../actions/orientationgroup.action";
import { ElectiveCourseFieldsActions } from "../../actions/electivecoursesfields.actions";
import { SCHOOLTYPE_INITIAL_STATE } from "../../store/schooltype/schooltype.initial-state";
import { ISchoolType, ISchoolTypeRecord, ISchoolTypeRecords } from "../../store/schooltype/schooltype.types";

@Component({
    selector: "reg-navbar",
    templateUrl: "navbar.component.html",
})

@Injectable() export default class NavbarComponent implements OnInit, OnDestroy {
    private authToken: string;
    private authRole: string;
    private schtype: number;
    private lockCapacity: BehaviorSubject<boolean>;
    private lockStudentsEpal: BehaviorSubject<boolean>;
    private lockStudentsGel: BehaviorSubject<boolean>;
    private userType: BehaviorSubject<string>;
    private loginInfo$: BehaviorSubject<ILoginInfoRecords>;
    private cuName: string;
    private cuser: any;
    private loginInfoSub: Subscription;

    private schooltype$: BehaviorSubject<ISchoolTypeRecords>;
    private schooltypeSub: Subscription;


    constructor(private _ngRedux: NgRedux<IAppState>
    ) {
        this.authToken = "";
        this.authRole = "";
        this.schtype = -1;
        this.lockCapacity = new BehaviorSubject(true);
        this.lockStudentsEpal = new BehaviorSubject(true);
        this.lockStudentsGel = new BehaviorSubject(true);
        this.cuName = "";
        this.loginInfo$ = new BehaviorSubject(LOGININFO_INITIAL_STATE);
        this.schooltype$ = new BehaviorSubject(SCHOOLTYPE_INITIAL_STATE);
        this.userType = new BehaviorSubject("");
    };

    ngOnInit() {
        this.loginInfoSub = this._ngRedux.select("loginInfo")
            .map(loginInfo => <ILoginInfoRecords>loginInfo)
            .subscribe(loginInfo => {
                if (loginInfo.size > 0) {
                    loginInfo.reduce(({}, loginInfoObj) => {
                        this.authToken = loginInfoObj.auth_token;
                        this.authRole = loginInfoObj.auth_role;
                        if (loginInfoObj.lock_capacity === 1)
                            this.lockCapacity.next(true);
                        else
                            this.lockCapacity.next(false);
                        if (loginInfoObj.lock_students_epal === 1)
                            this.lockStudentsEpal.next(true);
                        else
                            this.lockStudentsEpal.next(false);
                        if (loginInfoObj.lock_students_gel === 1)
                            this.lockStudentsGel.next(true);
                        else
                            this.lockStudentsGel.next(false);
                        this.cuName = loginInfoObj.cu_name;
                        if (this.cuName.slice(-1) === "1")
                          this.userType.next("gel");
                        else if (this.cuName.slice(-1) === "2")
                            this.userType.next("epal");
                        return loginInfoObj;
                    }, {});
                }

                this.loginInfo$.next(loginInfo);
            });

            this.schooltypeSub = this._ngRedux.select("schooltype")
            .map(schooltype => <ISchoolTypeRecords>schooltype)
            .subscribe(ecs => {
                if (ecs.size > 0) {
                      ecs.reduce(({}, type) => {
                          this.schtype = type.id ;
                        return type;
                    }, {});
                } else {
                }
                this.schooltype$.next(ecs);
            }, error => { console.log("error selecting schooltype"); });
    }

    ngOnDestroy() {
        if (this.loginInfoSub)
            this.loginInfoSub.unsubscribe();
    }
}
