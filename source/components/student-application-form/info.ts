import { NgRedux } from "@angular-redux/store";
import { Component, OnDestroy, OnInit } from "@angular/core";
import { Injectable } from "@angular/core";
import { Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";

import { LoginInfoActions } from "../../actions/logininfo.actions";
import { HelperDataService } from "../../services/helper-data-service";
import { LOGININFO_INITIAL_STATE } from "../../store/logininfo/logininfo.initial-state";
import { ILoginInfoRecords } from "../../store/logininfo/logininfo.types";
import { IAppState } from "../../store/store";

@Component({
    selector: "info",
    template: `

    <div class="loading" *ngIf="(showLoader$ | async) === true"></div>
    <div *ngIf="(loginInfo$ | async).size !== 0" style="margin-top: 30px; min-height: 500px;">
        <br/><br/>
        <p align="left"><strong>Ηλεκτρονικές δηλώσεις προτίμησης ΕΠΑΛ / ΓΕΛ για το νέο σχολικό έτος</strong></p>
        <p align="left">
          Η υπηρεσία υποβολής Αίτηση Εγγραφής -Δήλωσης Προτίμησης για {{lock_application_name | async}} δεν είναι διαθέσιμη αυτή την περίοδο.
          Αν έχετε υποβάλλει ήδη δήλωση μπορείτε να την δείτε και να την εκτυπώσετε σε μορφή PDF από την επιλογή "Υποβληθείσες Δηλώσεις" επάνω δεξιά</p>
        <div class="row" style="margin-top: 40px;">
        <div class="col-md-9 offset-md-3">
            <button class="btn-primary btn-lg isclickable" style="width: 12em;" (click)="signOut()">
            <span style="font-size: 0.9em;">Αποσύνδεση</span>
            </button>
        </div>
        </div>
      </div>

   `
})

@Injectable() export default class Info implements OnInit, OnDestroy {

    loginInfo$: BehaviorSubject<ILoginInfoRecords>;
    loginInfoSub: Subscription;
    private showLoader$: BehaviorSubject<boolean>;
    //private lock_application_epal: BehaviorSubject<number>;
    //private lock_application_gel: BehaviorSubject<number>;
    private lock_application_name: BehaviorSubject<string>;

    constructor(private _ata: LoginInfoActions,
        private _ngRedux: NgRedux<IAppState>,
        private router: Router,
        private _hds: HelperDataService
    ) {

        this.loginInfo$ = new BehaviorSubject(LOGININFO_INITIAL_STATE);
        this.showLoader$ = new BehaviorSubject(false);
        //this.lock_application_epal = new BehaviorSubject(1);
        //this.lock_application_gel = new BehaviorSubject(1);
        this.lock_application_name = new BehaviorSubject("test1");
    }

    ngOnDestroy() {
        if (this.loginInfoSub)
            this.loginInfoSub.unsubscribe();
    }

    ngOnInit() {
        this.loginInfoSub = this._ngRedux.select("loginInfo")
            .map(loginInfo => <ILoginInfoRecords>loginInfo)
            .subscribe(loginInfo => {
                this.loginInfo$.next(loginInfo);
                if (loginInfo.size > 0) {
                    loginInfo.reduce(({}, loginInfoObj) => {
                        if (loginInfoObj.lock_application_epal === 1 && loginInfoObj.lock_application_gel === 1)
                          this.lock_application_name.next("ΕΠΑΛ και ΓΕΛ");
                        else if (loginInfoObj.lock_application_epal === 1)
                          this.lock_application_name.next("ΕΠΑΛ");
                        else if (loginInfoObj.lock_application_gel === 1)
                          this.lock_application_name.next("ΓΕΛ");
                        return loginInfoObj;
                    }, {});
                }



            }, error => { console.log("error selecting loginInfo"); });
    }

    signOut() {
        this.showLoader$.next(true);
        this._hds.signOut().then(data => {
            this._ata.initLoginInfo();
            this.router.navigate([""]);
            this.showLoader$.next(false);
        }).catch(err => {
            this.showLoader$.next(false);
            console.log(err);
        });
    }
}
