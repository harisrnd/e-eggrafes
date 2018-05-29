import { NgRedux } from "@angular-redux/store";
import { Component, OnDestroy, OnInit } from "@angular/core";
import { Injectable } from "@angular/core";
import { ActivatedRoute, Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";

import { API_ENDPOINT } from "../../app.settings";
import { DIDE_ROLE } from "../../constants";
import { HelperDataService } from "../../services/helper-data-service";
import { LOGININFO_INITIAL_STATE } from "../../store/logininfo/logininfo.initial-state";
import { ILoginInfoRecords } from "../../store/logininfo/logininfo.types";
import { IAppState } from "../../store/store";

@Component({
    selector: "didegel-reports",
    template: `
    <div class="reports-container">

        <h5>Επιλογή Αναφοράς για ΓΕΛ</h5>

        <div *ngIf = "userRole == 'dide' ">
            <button type="button" class="btn btn-block"  (click)="nav_to_reportpath(0)"><i class="fa fa-file-text"></i> Τοποθετήσεις Μαθητών</button>
        </div>
        <div *ngIf = "userRole == 'dide' ">
            <button type="button" class="btn btn-block"  (click)="nav_to_reportpath(1)"><i class="fa fa-file-text"></i> Πληρότητα τμημάτων</button>
        </div>

    </div>

   `
})

@Injectable() export default class DideGelReports implements OnInit, OnDestroy {

    private loginInfo$: BehaviorSubject<ILoginInfoRecords>;
    private loginInfoSub: Subscription;
    private apiEndPoint = API_ENDPOINT;
    private userRole: string;

    constructor(
        private _ngRedux: NgRedux<IAppState>,
        private _hds: HelperDataService,
        private activatedRoute: ActivatedRoute,
        private router: Router
    ) {
        this.loginInfo$ = new BehaviorSubject(LOGININFO_INITIAL_STATE);
    }

    ngOnInit() {

        this.loginInfoSub = this._ngRedux.select("loginInfo")
            .map(loginInfo => <ILoginInfoRecords>loginInfo)
            .subscribe(loginInfo => {
                if (loginInfo.size > 0) {
                    loginInfo.reduce(({}, loginInfoObj) => {
                        if (loginInfoObj.auth_role === DIDE_ROLE ) {
                            this.userRole = loginInfoObj.auth_role;
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
            this.router.navigate(["/dide/didegel-distrib-report"]);
        }
        else if (repId === 1) {
            this.router.navigate(["/dide/didegel-complet-report"]);
        }
    }

}
