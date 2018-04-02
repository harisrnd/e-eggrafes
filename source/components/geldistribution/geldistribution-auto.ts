import { NgRedux } from "@angular-redux/store";
import { Component, OnDestroy, OnInit } from "@angular/core";
import { Injectable } from "@angular/core";
import { ActivatedRoute, Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";

import { API_ENDPOINT } from "../../app.settings";
import { HelperDataService } from "../../services/helper-data-service";
import { LOGININFO_INITIAL_STATE } from "../../store/logininfo/logininfo.initial-state";
import { ILoginInfoRecords } from "../../store/logininfo/logininfo.types";
import { IAppState } from "../../store/store";

@Component({
    selector: "gel-distribution-auto",
    template: `

    <h5> >Αυτόματη Τοποθέτηση Μαθητών (Β'/Γ'/Δ' Λυκείου) <br></h5>

    <div
      class = "loading" *ngIf="showLoader | async" >
    </div>

    <div style="min-height: 400px;">

    <div id="distributionNotice" (onHidden)="onHidden()" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header {{modalHeader | async}}" >
              <h3 class="modal-title pull-left"><i class="fa fa-check-square-o"></i>&nbsp;&nbsp;{{ modalTitle | async }}</h3>
            <button type="button" class="close pull-right" aria-label="Close" (click)="hideModal('#distributionNotice')">
              <span aria-hidden="true"><i class="fa fa-times"></i></span>
            </button>
          </div>
          <div class="modal-body">
              <p>{{ modalText | async }}</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Κλείσιμο</button>
          </div>
        </div>
      </div>
    </div>

    <div id="distributionWaitingNotice" (onHidden)="onHidden('#distributionWaitingNotice')" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header modal-header-warning">
            <h3 class="modal-title pull-left"><i class="fa fa-check-square-o"></i>&nbsp;&nbsp;Τοποθέτηση μαθητών σε Β'/Γ'/Δ' τάξη</h3>
            <button type="button" class="close pull-right" aria-label="Close" (click)="hideModal('#distributionWaitingNotice')">
              <span aria-hidden="true"><i class="fa fa-times"></i></span>
            </button>
          </div>
          <div class="modal-body">
            <p>Παρακαλώ περιμένετε...Η εκτέλεση της αυτόματης τοποθέτησης μαθητών (σε Β'/ Γ'/ Δ' Λυκείου) ενδέχεται να <strong>διαρκέσει μερικά λεπτά</strong>.
            Παρακαλώ <strong>μην</strong> εκτελείτε οποιαδήποτε <strong>ενέργεια μετακίνησης</strong> στον φυλλομετρητή σας, μέχρι να ολοκληρωθεί η τοποθέτηση.
            Παρακαλώ κλείστε αυτό το μήνυμα μόλις το διαβάσετε.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Κλείσιμο</button>
          </div>
        </div>
      </div>
    </div>

    <br><br>

    <div>
        <div class="col-md-6">
          <button type="submit" class="btn btn-lg btn-block"  *ngIf="(loginInfo$ | async).size !== 0"  (click)="runDistribution()" >
              Εκτέλεση Τοποθέτησης Μαθητών<span class="glyphicon glyphicon-menu-right"></span>
          </button>
        </div>
        <br>

    </div>

    </div>

   `
})

@Injectable() export default class GelDistributionAuto implements OnInit, OnDestroy {

    private loginInfo$: BehaviorSubject<ILoginInfoRecords>;
    private modalTitle: BehaviorSubject<string>;
    private modalText: BehaviorSubject<string>;
    private modalHeader: BehaviorSubject<string>;
    private settings$: BehaviorSubject<any>;
    private loginInfoSub: Subscription;
    private settingsSub: Subscription;
    private apiEndPoint = API_ENDPOINT;
    private minedu_userName: string;
    private minedu_userPassword: string;
    private distStatus = "READY";
    private directorViewDisabled: boolean;
    private applicantsResultsDisabled: boolean;
    private showLoader: BehaviorSubject<boolean>;

    constructor(
        private _ngRedux: NgRedux<IAppState>,
        private _hds: HelperDataService,
        private activatedRoute: ActivatedRoute,
        private router: Router) {
        this.loginInfo$ = new BehaviorSubject(LOGININFO_INITIAL_STATE);
        this.modalTitle = new BehaviorSubject("");
        this.modalText = new BehaviorSubject("");
        this.modalHeader = new BehaviorSubject("");
        this.settings$ = new BehaviorSubject([{}]);
        this.showLoader = new BehaviorSubject(false);
    }

    public showModal(popupMsgId): void {
        (<any>$(popupMsgId)).modal("show");
    }

    public hideModal(popupMsgId): void {
        (<any>$(popupMsgId)).modal("hide");
    }

    public onHidden(popupMsgId): void {

    }

    ngOnDestroy() {
        (<any>$("#distributionWaitingNotice")).remove();
        (<any>$("#distributionNotice")).remove();
        if (this.loginInfoSub)
            this.loginInfoSub.unsubscribe();
        if (this.settingsSub)
            this.settingsSub.unsubscribe();
    }

    ngOnInit() {
        (<any>$("#distributionWaitingNotice")).appendTo("body");
        (<any>$("#distributionNotice")).appendTo("body");
        this.loginInfoSub = this._ngRedux.select("loginInfo")
            .map(loginInfo => <ILoginInfoRecords>loginInfo)
            .subscribe(loginInfo => {
                if (loginInfo.size > 0) {
                    loginInfo.reduce(({}, loginInfoObj) => {
                        this.minedu_userName = loginInfoObj.minedu_username;
                        this.minedu_userPassword = loginInfoObj.minedu_userpassword;
                        return loginInfoObj;
                    }, {});
                }
                this.loginInfo$.next(loginInfo);
            }, error => console.log("error selecting loginInfo"));

        //this.retrieveSettings();
    }


    runDistribution() {

        this.showLoader.next(true);
        this.distStatus = "STARTED";
        this.showModal("#distributionWaitingNotice");

        /*
        this._hds.makeAutoDistribution(this.minedu_userName, this.minedu_userPassword)
            .then(msg => {
                this.modalTitle.next("Τοποθέτηση Μαθητών");
                this.modalText.next("Η τοποθέτηση ολοκληρώθηκε με επιτυχία!");
                this.modalHeader.next("modal-header-success");
                this.showModal("#distributionNotice");

                if (this.distStatus !== "ERROR")
                    this.distStatus = "FINISHED";
            })
            .catch(err => {
                console.log(err);
                this.distStatus = "ERROR";

                this.modalTitle.next("Τοποθέτηση Μαθητών");
                this.modalText.next("Αποτυχία τοποθέτησης. Προσπαθήστε ξανά. Σε περίπτωση που το πρόβλημα παραμένει, παρακαλώ επικοινωνήστε με το διαχειριστή του συστήματος.");
                this.modalHeader.next("modal-header-danger");
                this.showModal("#distributionNotice");
            });
        */
        let status = 0;
        this._hds.makeAutoDistribution().subscribe(x => {
            this.showLoader.next(false);
            status = x.error_code;
            if (status == 1000)  {
              this.modalTitle.next("Τοποθέτηση Μαθητών");
              this.modalText.next(("ΣΦΑΛΜΑ: Για να μπορεί να εκτελεστεί η Τοποθέτηση Μαθητών, θα πρέπει ο Διαχειριστής να έχει ΑΠΕΝΕΡΓΟΠΟΙΗΣΕΙ  ") +
                  ("τη δυνατότητα των Διευθυντών της προβολής κατανομής των μαθητών του σχολείου τους."));
              this.modalHeader.next("modal-header-warning");
              this.showModal("#distributionNotice");
            }
            else if (status == 1001) {
              this.modalTitle.next("Τοποθέτηση Μαθητών");
              this.modalText.next(("ΣΦΑΛΜΑ: Για να μπορεί να εκτελεστεί η Τοποθέτηση Μαθητών, θα πρέπει ο Διαχειριστής να έχει ΑΠΕΝΕΡΓΟΠΟΙΗΣΕΙ    ") +
                  ("τη δυνατότητα της προβολής αποτελεσμάτων κατανομής από τους μαθητές."));
              this.modalHeader.next("modal-header-warning");
              this.showModal("#distributionNotice");
            }
        },
        error => {
            console.log("No AutoDistribution");
            this.showLoader.next(false);
            //console.log(status);

            //else if (status == 1002) {
              this.modalTitle.next("Τοποθέτηση Μαθητών");
              this.modalText.next(("Σφάλμα σε ερωτήματα στη βάση δεδομένων. Ο Διαχειριστής του συστήματος θα πρέπει να έχει αδειάσει τον πίνακα αποτελεσμάτων."));
              this.modalHeader.next("modal-header-warning");
              this.showModal("#distributionNotice");
            //}


        });
    }


    retrieveSettings() {

        this.settingsSub = this._hds.retrieveAdminSettings("epal", this.minedu_userName, this.minedu_userPassword).subscribe(data => {
            this.settings$.next(data);
            this.directorViewDisabled = Boolean(Number(this.settings$.value["directorViewDisabled"]));
            this.applicantsResultsDisabled = Boolean(Number(this.settings$.value["applicantsResultsDisabled"]));

            if (this.directorViewDisabled === false) {
                this.modalTitle.next("Τοποθέτηση Μαθητών");
                this.modalText.next(("ΠΡΟΣΟΧΗ: Για να μπορείτε να εκτελέσετε την Τοποθέτηση Μαθητών, παρακαλώ πηγαίνετε στις Ρυθμίσεις και ΑΠΕΝΕΡΓΟΠΟΙΗΣΤΕ  ") +
                    ("τη δυνατότητα των Διευθυντών της προβολής κατανομής των μαθητών του σχολείου τους."));
                this.modalHeader.next("modal-header-warning");
                this.showModal("#distributionNotice");
            }
            else if (this.applicantsResultsDisabled === false) {
                this.modalTitle.next("Τοποθέτηση Μαθητών");
                this.modalText.next(("ΠΡΟΣΟΧΗ: Για να μπορείτε να εκτελέσετε την Τοποθέτηση Μαθητών, παρακαλώ πηγαίνετε στις Ρυθμίσεις και ΑΠΕΝΕΡΓΟΠΟΙΗΣΤΕ  ") +
                    ("τη δυνατότητα της προβολής αποτελεσμάτων κατανομής από τους μαθητές."));
                this.modalHeader.next("modal-header-warning");
                this.showModal("#distributionNotice");
            }
        },
            error => {
                this.settings$.next([{}]);
                console.log("Error Getting MinisterRetrieveSettings");
            });
    }

}
