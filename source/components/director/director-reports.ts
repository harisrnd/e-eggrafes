import { NgRedux } from "@angular-redux/store";
import { Component, OnDestroy, OnInit } from "@angular/core";
import { Injectable } from "@angular/core";
import { ActivatedRoute, Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";

import { API_ENDPOINT } from "../../app.settings";
import { SCHOOL_ROLE } from "../../constants";
import { HelperDataService } from "../../services/helper-data-service";
import { LOGININFO_INITIAL_STATE } from "../../store/logininfo/logininfo.initial-state";
import { ILoginInfoRecords } from "../../store/logininfo/logininfo.types";
import { IAppState } from "../../store/store";

@Component({
    selector: "director-reports",
    template: `
    <div class="reports-container">

        <h5>Επιλογή Αναφοράς</h5>

        <div *ngIf = "userRole == 'director' ">
            <button type="button" class="btn btn-block"  (click)="nav_to_reportpath(0)"><i class="fa fa-file-text"></i> Δυναμική Τμημάτων</button>
        </div>

    </div>

   `
})

@Injectable() export default class DirectorReports implements OnInit, OnDestroy {

    private loginInfo$: BehaviorSubject<ILoginInfoRecords>;
    private loginInfoSub: Subscription;
    private apiEndPoint = API_ENDPOINT;
    //private minedu_userName: string;
    //private minedu_userPassword: string;
    private userRole: string;

    constructor(
        private _ngRedux: NgRedux<IAppState>,
        private _hds: HelperDataService,
        private activatedRoute: ActivatedRoute,
        private router: Router
    ) {
        this.loginInfo$ = new BehaviorSubject(LOGININFO_INITIAL_STATE);
        //this.minedu_userName = "";
        //this.userRole = DIRECTOR_ROLE;
    }

    ngOnInit() {

        this.loginInfoSub = this._ngRedux.select("loginInfo")
            .map(loginInfo => <ILoginInfoRecords>loginInfo)
            .subscribe(loginInfo => {
                if (loginInfo.size > 0) {
                    loginInfo.reduce(({}, loginInfoObj) => {
                        //this.minedu_userName = loginInfoObj.minedu_username;
                        //this.minedu_userPassword = loginInfoObj.minedu_userpassword;
                        if (loginInfoObj.auth_role === SCHOOL_ROLE ) {
                            this.userRole = loginInfoObj.auth_role;
                            //this.minedu_userName = loginInfoObj.auth_token;
                            //this.minedu_userPassword = loginInfoObj.auth_token;
                        }
                        return loginInfoObj;
                    }, {});
                }
                this.loginInfo$.next(loginInfo);
            }, error => console.log("error selecting loginInfo"));
    }

    ngOnDestroy() {
        if (this.loginInfoSub) {
            this.loginInfoSub.unsubscribe();
        }
    }

    nav_to_reportpath(repId) {
        if (repId === 0) {
            //this.router.navigate(["/school/report-capacity", repId]);
            this.router.navigate(["/school/report-capacity"]);
        }
        /*
        else if (repId === 1) {
            this.router.navigate(["..."]);
        }
        */
    }

}
