import { NgRedux } from "@angular-redux/store";
import { Component, OnDestroy, OnInit } from "@angular/core";
import { ActivatedRoute, Params, Router } from "@angular/router";
import { CookieService } from "ngx-cookie";
import { BehaviorSubject, Subscription } from "rxjs/Rx";

import { LoginInfoActions } from "../actions/logininfo.actions";
import { API_ENDPOINT, API_ENDPOINT_PARAMS } from "../app.settings";
import { HelperDataService } from "../services/helper-data-service";
import { LOGININFO_INITIAL_STATE } from "../store/logininfo/logininfo.initial-state";
import { ILoginInfoRecords } from "../store/logininfo/logininfo.types";
import { IAppState } from "../store/store";

@Component({
    selector: "school-home",
    template: `
<div style="min-height: 300px; margin-top: 60px;">
    <div *ngIf="(errorCode$ | async) != ''">
        <div [ngSwitch]="errorCode$ | async">
            <p class="text-danger" *ngSwitchCase="5001">Προέκυψε σφάλμα κατά την διαδικασία αυθεντικοποίησης σας.</p>
            <p class="text-danger" *ngSwitchCase="5002">Πρέπει να συνδεθείτε με λογαριασμό του Πανελλήνιου Σχολικού Δικτύου, για να χρησιμοποιήσετε την εφαρμογή.</p>
            <p class="text-danger" *ngSwitchCase="5003">Πρέπει να συνδεθείτε με τον λογαριασμό που χρησιμοποιείτε για να συνδεθείτε στο ΠΣΔ, για να χρησιμοποιήσετε την εφαρμογή.</p>
            <p class="text-danger" *ngSwitchCase="5004">Ο ρόλος που αντιστοιχεί στον λογαριασμό σας στο Πανελλήνιο Σχολικό Δίκτυο δεν επιτρέπεται να χρησιμοποιήσετε την εφαρμογή.</p>
            <p class="text-danger" *ngSwitchCase="5005">Προέκυψε σφάλμα κατά την διαδικασία αυθεντικοποίησης σας.</p>
            <p class="text-danger" *ngSwitchCase="6000">Προέκυψε σφάλμα κατά την διαδικασία αυθεντικοποίησης σας. <br/>Παρακαλώ συνδεθείτε χρησιμοποιώντας τα στοιχεία του λογαριασμού με τον οποίο συνδέεστε στο Myschool.</p>
            <p class="text-danger" *ngSwitchDefault>Προέκυψε σφάλμα {{ errorCode$ | async }}</p>
        </div>
        <div class="alert alert-danger" role="alert">Για να επαναλάβετε τη διαδικασία σύνδεσης πρέπει πρώτα να αποσυνδεθείτε.</div>
        <div class="row">
            <div class="col-sm-4">&nbsp;</div>
            <div class="col-sm-4">
                <button type="submit" class="btn btn-lg btn-block isclickable" (click)="casSignOut()">Αποσύνδεση</button>
            </div>
        </div>
    </div>
    <div *ngIf="(errorCode$ | async) == ''">
        <form method = "POST" action="{{apiEndPoint}}/cas/login{{apiEndPointParams}}" #form>
            <!-- <input type="hidden" name="X-oauth-enabled" value="true"> -->
            <div *ngFor="let loginInfoToken$ of loginInfo$ | async; let i=index"></div>
            <div class="row">
                <div *ngIf="!authToken" class="col-md-8 offset-md-4">
                    <button type="submit" class="btn-primary btn-lg" (click)="form.submit()">
                    Είσοδος μέσω Π.Σ.Δ<span class="glyphicon glyphicon-menu-right"></span>
                    </button>
                </div>
            </div>

            <br/><br/>

            <!--
            <p>Θα ζητηθούν <i><strong>Όνομα Χρήστη</strong></i> και <i><strong>Κωδικός Πρόσβασης</strong></i> για τα οποία θα πρέπει να δοθούν αυτά που χρησιμοποιεί το σχολείο σας
              για την πρόσβαση στην <strong>υπηρεσία Ηλεκτρονικού Ταχυδρομείου του Πανελλήνιου Σχολικού Δικτύου - Επίσημος Λογαριασμός της Μονάδας</strong>.
              Aν δεν γίνεται δεκτός ο κωδικός σας και εφόσον δοκιμάσετε και επιβεβαιώσετε την ορθότητα του
              στο <a href = "http://www.sch.gr"> www.sch.gr</a> ή  στο <a href = "https://webmail.sch.gr"> https://webmail.sch.gr</a>
              μπορείτε να αναφέρετε το πρόβλημα άμεσα στο Πανελλήνιο Σχολικό Δίκτυο με μήνυμα σας στο info@sch.gr.
            </p>
            -->

            <!--
            <p>
            Για είσοδο στην εφαρμογή, απαιτείται Όνομα Χρήστη και Κωδικός Πρόσβασης που χρησιμοποιείτε για την πρόσβαση της σχολικής μονάδας
            ή της Διεύθυνσης Δευτεροβάθμιας Εκπαίδευσης ή της Περιφερειακής Διεύθυνσης Εκπαίδευσης
            στην υπηρεσία <strong>Ηλεκτρονικού Ταχυδρομείου στο Πανελλήνιο Σχολικό Δίκτυο –Επίσημος Λογαριασμός Μονάδας.<strong>
            Aν δεν γίνεται δεκτός ο κωδικός σας και εφόσον δοκιμάσετε και επιβεβαιώσετε την ορθότητα του στο <a href = "http://www.sch.gr">www.sch.gr ή
            στο https://webmail.sch.gr μπορείτε να αναφέρετε το πρόβλημα άμεσα στο Πανελλήνιο Σχολικό Δίκτυο
            με μήνυμα σας στο <a href = "info@sch.gr">info@sch.gr.
            </p>
            -->
            <p>
            Για είσοδο στην εφαρμογή, απαιτoύνται <strong>Όνομα Χρήστη</strong> και <strong>Κωδικός Πρόσβασης</strong> που χρησιμοποιούνται
            για την πρόσβαση της σχολικής μονάδας ή της Διεύθυνσης Δευτεροβάθμιας Εκπαίδευσης ή της Περιφερειακής Διεύθυνσης Εκπαίδευσης
            στην υπηρεσία <strong>Ηλεκτρονικού Ταχυδρομείου στο Πανελλήνιο Σχολικό Δίκτυο – Επίσημος Λογαριασμός Μονάδας.</strong>
            Aν δεν γίνεται δεκτός ο κωδικός σας και εφόσον δοκιμάσετε και επιβεβαιώσετε την ορθότητα του στο <a href = "http://www.sch.gr"> www.sch.gr</a>
            ή στο <a href = "https://webmail.sch.gr"> https://webmail.sch.gr</a> μπορείτε να αναφέρετε το πρόβλημα άμεσα
            στο Πανελλήνιο Σχολικό Δίκτυο με μήνυμα σας στο <i>info@sch.gr</i>.
            </p>

            <p>
            Σε περίπτωση που αντιμετωπίζετε πρόβλημα στη χρήση της εφαρμογής, μπορείτε να στείλετε ηλεκτρονικό μήνυμα στη διεύθυνση
            <i>egel@minedu.gov.gr</i> ή να καλέσετε στο <strong>210-3443911</strong>.
            </p>



        </form>


        <p align="left"><strong> Οδηγίες προς Διευθυντές</strong></p>
            <ul class="list-group">
            <li class="list-group-item isclickable evenout"  >
                <a class="col-md-12" style="font-size: 0.8em; font-weight: bold;" href="../pdfs/files/MANUAL_epal_sxoleia.pdf" target="_blank">Οδηγίες προς Διευθυντές ΕΠΑ.Λ. σχετικά με τη Δήλωση της Δυναμικής της Σχολικής Μονάδας σε Αίθουσες</a>
            </li>
            <li class="list-group-item isclickable evenout"  >
                <a class="col-md-12" style="font-size: 0.8em; font-weight: bold;" href="../pdfs/files/odigiessxoleio.pdf" target="_blank">Οδηγίες προς Διευθυντές ΕΠΑ.Λ. σχετικά με τα αποτελέσματα των Ηλεκτρονικών Δηλώσεων Προτίμησης</a>
            </li>
            <li class="list-group-item isclickable evenout"  >
                <a class="col-md-12" style="font-size: 0.8em; font-weight: bold;"
                href="../pdfs/files/odigiessxoleiogel.pdf" target="_blank">Οδηγίες προς Διευθυντές ΓΕ.Λ. σχετικά με τα αποτελέσματα των Ηλεκτρονικών Δηλώσεων Προτίμησης</a>
            </li>
            <li class="list-group-item isclickable evenout"  >
                <a class="col-md-12" style="font-size: 0.8em; font-weight: bold;"
                href="../pdfs/files/e-eggrafes_odhgies_dnshs.pdf" target="_blank">Οδηγίες προς Διευθυντές ΔΔΕ σχετικά με τη χρήση της εφαρμογής για κατανομή των μαθητών Α’ Λυκείου σε Γενικά Λύκεια</a>
            </li>
            </ul>


    </div>
</div>
`
})

export default class SchoolHome implements OnInit, OnDestroy {
    private authToken: string;
    private errorCode$: BehaviorSubject<string>;
    private authRole: string;
    private name: any;
    private xcsrftoken: any;
    private loginInfo$: BehaviorSubject<ILoginInfoRecords>;
    private loginInfoSub: Subscription;
    private queryParamSub: Subscription;
    private apiEndPoint = API_ENDPOINT;
    private apiEndPointParams = API_ENDPOINT_PARAMS;

    constructor(
        private _ata: LoginInfoActions,
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
        this.errorCode$ = new BehaviorSubject("");
    };

    ngOnDestroy() {
        if (this.loginInfoSub)
            this.loginInfoSub.unsubscribe();
        if (this.queryParamSub)
            this.queryParamSub.unsubscribe();
    };

    ngOnInit() {
        this.loginInfoSub = this._ngRedux.select("loginInfo")
            .map(loginInfo => <ILoginInfoRecords>loginInfo)
            .subscribe(linfo => {
                if (linfo.size > 0) {
                    linfo.reduce(({ }, loginInfoObj) => {
                        this.authToken = loginInfoObj.auth_token;
                        this.authRole = loginInfoObj.auth_role;
                        if (this.authToken && this.authToken.length > 0) {
                            if (this.authRole === "director") {
                                this.router.navigate(["/school/director-buttons"]);
                                //this.router.navigate(["/school/directorgym-view"]);

                            }
                            else if (this.authRole === "director_gel")
                            {
                                this.router.navigate(["/school/directorgel-view"]); 
                            }
                            else if (this.authRole === "director_gym" || this.authRole === "director_gymlt" )
                                {
                                 
                                    this.router.navigate(["/school/directorgym-view"]);                      
                                }  
                            else if (this.authRole === "pde")
                             {
                                this.router.navigate(["/school/perfecture-view"]);                        
                                 }
                            else if (this.authRole === "dide")
                                this.router.navigate(["/school/school-type-selection"]);

                        }
                        return loginInfoObj;
                    }, {});
                }

                this.loginInfo$.next(linfo);
            });

        // subscribe to router event
        this.queryParamSub = this.activatedRoute.queryParams.subscribe((params: Params) => {
            if (params) {
                this.authToken = params["auth_token"];
                this.authRole = params["auth_role"];
                this.errorCode$.next((params["error_code"] === undefined) ? "" : params["error_code"]);
            }
            if (this.authToken && this.authRole && this.errorCode$.getValue() === "") {
                this._ata.getloginInfo({ auth_token: this.authToken, auth_role: this.authRole });
            }
        });
    }

    getCookie(key: string) {
        return this._cookieService.get(key);
    }

    removeCookie(key: string) {
        return this._cookieService.remove(key);
    }

    checkvalidation() {

    }

    /**
     * Logout from CAS only helper
     */
    casSignOut() {
        this._hds.casSignOut().then(data => {
            this._ata.initLoginInfo();
            this.authToken = "";
            this.authRole = "";
            window.location.assign((<any>data).next);
        }).catch(err => {
            console.log(err);
        });
    }
}
