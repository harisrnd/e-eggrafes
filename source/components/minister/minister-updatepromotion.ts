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
    selector: "minister-updatepromotion",
    template: `

    <h5> >Ενημέρωση προαγωγής / απόλυσης <br></h5>

    <div
      class = "loading" *ngIf="showLoader | async" >
    </div>

    <div style="min-height: 400px;">

    <div id="promotionNotice" (onHidden)="onHidden()" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header {{modalHeader | async}}" >
              <h3 class="modal-title pull-left"><i class="fa fa-check-square-o"></i>&nbsp;&nbsp;{{ modalTitle | async }}</h3>
            <button type="button" class="close pull-right" aria-label="Close" (click)="hideModal('#promotionNotice')">
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

    <div id="promotionWaitingNotice" (onHidden)="onHidden('#promotionWaitingNotice')" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header modal-header-warning">
            <h3 class="modal-title pull-left"><i class="fa fa-check-square-o"></i>&nbsp;&nbsp;Τοποθέτηση μαθητών σε Β'/Γ'/Δ' τάξη</h3>
            <button type="button" class="close pull-right" aria-label="Close" (click)="hideModal('#promotionWaitingNotice')">
              <span aria-hidden="true"><i class="fa fa-times"></i></span>
            </button>
          </div>
          <div class="modal-body">
            <p>Παρακαλώ περιμένετε...Η ενημέρωση προαγωγής / απόλυσης μαθητών ενδέχεται να <strong>διαρκέσει μερικά λεπτά</strong>.
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
          <button type="submit" class="btn btn-lg btn-block"  *ngIf="(loginInfo$ | async).size !== 0"  (click)="updatePromotionNow()" >
              Ενημέρωση Προαγωγής / Απόλυσης<span class="glyphicon glyphicon-menu-right"></span>
          </button>
        </div>
        <br>

        <div class="col-md-6">
          <button type="submit" class="btn btn-lg btn-block"  *ngIf="(loginInfo$ | async).size !== 0"  (click)="goToSecondPeriod()" >
              Μετάπτωση Αιτήσεων σε Β' περίδο<span class="glyphicon glyphicon-menu-right"></span>
          </button>
        </div>
        <br>

    </div>

    </div>

   `
})

@Injectable() export default class UpdatePromotion implements OnInit, OnDestroy {

    private loginInfo$: BehaviorSubject<ILoginInfoRecords>;
    private modalTitle: BehaviorSubject<string>;
    private modalText: BehaviorSubject<string>;
    private modalHeader: BehaviorSubject<string>;
    private showLoader: BehaviorSubject<boolean>;
    private loginInfoSub: Subscription;
    private ServiceStudentCertifSub: Subscription;
    //private wsIdentSub: Subscription;
    private minedu_userName: string;
    private minedu_userPassword: string;
    private wsEnabled: BehaviorSubject<number>;

    constructor(
        private _ngRedux: NgRedux<IAppState>,
        private _hds: HelperDataService,
        private activatedRoute: ActivatedRoute,
        private router: Router) {
        this.loginInfo$ = new BehaviorSubject(LOGININFO_INITIAL_STATE);
        this.modalTitle = new BehaviorSubject("");
        this.modalText = new BehaviorSubject("");
        this.modalHeader = new BehaviorSubject("");
        this.showLoader = new BehaviorSubject(false);
        this.wsEnabled = new BehaviorSubject(-1);

        //this.wsIdentSub = this._hds.isWS_ident_enabled().subscribe(z => {
        //    this.wsEnabled.next(Number(z.res)) ;
        //});
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
        (<any>$("#promotionWaitingNotice")).remove();
        (<any>$("#promotionNotice")).remove();
        if (this.loginInfoSub)
            this.loginInfoSub.unsubscribe();
        if (this.ServiceStudentCertifSub)
            this.ServiceStudentCertifSub.unsubscribe();
        //if (this.wsIdentSub)
        //   this.wsIdentSub.unsubscribe();
    }

    ngOnInit() {
        (<any>$("#promotionWaitingNotice")).appendTo("body");
        (<any>$("#promotionNotice")).appendTo("body");

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

    }


    updatePromotionNow() {

        //this.showModal("#promotionWaitingNotice");

        //κλήση myschool web service
        //if (this.wsEnabled.getValue() === 1 )  {

                this.showLoader.next(true);

                let id = 0;

                this.ServiceStudentCertifSub = this._hds.getServiceAllStudentPromotion(this.minedu_userName, this.minedu_userPassword)
                .subscribe(data => {
                    //if (data.data!=null)  {
                      //let test = data.data["studentId"];
                      console.log("Success");
                      this.showLoader.next(false);
                    //}
                },
                error => {
                    console.log("Error Getting StudentPromotion from Web Service");

                    let mTitle = "Αποτυχία Eνημέρωσης Προαγωγής Μαθητή.";
                    let mText = "Αποτυχία κλήσης του web service προαγωγής / απόλυσης μαθητή. " +
                      "Προσπαθήστε ξανά. Σε περίπτωση που το πρόβλημα επιμείνει, παρακαλώ επικοινωνήστε με την ομάδα υποστήριξης.";
                    let mHeader = "modal-header-danger";
                    this.modalTitle.next(mTitle);
                    this.modalText.next(mText);
                    this.modalHeader.next(mHeader);
                    this.showModal("#promotionNotice");
                    (<any>$(".loading")).remove();

                    this.showLoader.next(false);
                    return;
                });

        }

    //}


    goToSecondPeriod() {

                this.showLoader.next(true);

                let id = 0;

                this.ServiceStudentCertifSub = this._hds.transitionToBPeriod(this.minedu_userName, this.minedu_userPassword)
                .subscribe(data => {
                      let mTitle = "Επιτυχία Μετάπτωσης";
                      let mText = "H μετάπτωση αιτήσεων σε Β' περίοδο έγινε με επιτυχία.";
                      let mHeader = "modal-header-success";
                      this.modalTitle.next(mTitle);
                      this.modalText.next(mText);
                      this.modalHeader.next(mHeader);
                      this.showModal("#promotionNotice");
                      (<any>$(".loading")).remove();

                      this.showLoader.next(false);
                },
                error => {
                    console.log("Error Getting goToSecondPeriod from Web Service");

                    let mTitle = "Αποτυχία Μετάπτωσης.";
                    let mText = "Αποτυχία μετάπτωσης. " +
                      "Προσπαθήστε ξανά. Σε περίπτωση που το πρόβλημα επιμείνει, παρακαλώ επικοινωνήστε με την ομάδα υποστήριξης.";
                    let mHeader = "modal-header-danger";
                    this.modalTitle.next(mTitle);
                    this.modalText.next(mText);
                    this.modalHeader.next(mHeader);
                    this.showModal("#promotionNotice");
                    (<any>$(".loading")).remove();

                    this.showLoader.next(false);
                    return;
                });

        }

    //}


}
