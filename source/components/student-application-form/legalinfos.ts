import { Location } from "@angular/common";
import { Component, Injectable } from "@angular/core";
import { Router } from "@angular/router";
import { NgRedux } from "@angular-redux/store";
import { DIDE_ROLE, MINISTRY_ROLE, PDE_ROLE, SCHOOL_ROLE, STUDENT_ROLE, SCHOOLGEL_ROLE, SCHOOLGYM_ROLE } from "../../constants";
import { LOGININFO_INITIAL_STATE } from "../../store/logininfo/logininfo.initial-state";
import { ILoginInfoRecords } from "../../store/logininfo/logininfo.types";
import { IAppState } from "../../store/store";
import { LoginInfoActions } from "../../actions/logininfo.actions";
import { BehaviorSubject, Subscription } from "rxjs/Rx";


@Component({
    selector: "legal-info",
    template: `
        <p align="left"><strong> Νομοθεσία  </strong></p>
        <ul class="list-group">
        <li class="list-group-item isclickable evenout"  >
            <a class="col-md-12" style="font-size: 0.8em; font-weight: bold;" href="../pdfs/files/ypourgikh.pdf" target="_blank">Υπουργική Απόφαση - αριθμ. 10645/ΓΔ4/23-01-2018</a>
        </li>
        <li class="list-group-item isclickable oddout" >
            <a class="col-md-12" style="font-size: 0.8em; font-weight: bold;" href="../pdfs/files/egkyklios.pdf" target="_blank">Εγκύκλιος του Υ.Π.Π.Ε.Θ.- αρ.πρωτ. Φ1α/73611/Δ4/09-05-2018 </a>
        </li>
        </ul>

    <br>
    <br>
    <p align="left"><strong> Χρήσιμες Πληροφορίες για μαθητές ΓΕ.Λ. </strong></p>
        <ul class="list-group">
        <li class="list-group-item isclickable evenout"  >
            <a class="col-md-12" style="font-size: 0.8em; font-weight: bold;" href="../pdfs/files/infosgel.pdf" target="_blank">Ενημερωτικά Στοιχεία</a>
        </li>
        <li class="list-group-item isclickable evenout"  >
            <a class="col-md-12" style="font-size: 0.8em; font-weight: bold;" href="http://www.minedu.gov.gr/lykeio-2/to-thema-lykeio" target="_blank">Πληροφορίες για το Λύκειο </a>
        </li>
        </ul>




    <br>
    <br>
    <p align="left"><strong> Χρήσιμες Πληροφορίες για μαθητές ΕΠΑ.Λ. </strong></p>
        <ul class="list-group">
        <li class="list-group-item isclickable evenout"  >
            <a class="col-md-12" style="font-size: 0.8em; font-weight: bold;" href="../pdfs/files/infos.pdf" target="_blank">Ενημερωτικά Στοιχεία</a>
        </li>
        <li class="list-group-item isclickable oddout"  >
            <a class="col-md-12" style="font-size: 0.8em; font-weight: bold;" href="../pdfs/files/diptixo.pdf" target="_blank">Η Επαγγελματική Εκπαίδευση αναβαθμίζεται</a>
        </li>
        <li class="list-group-item isclickable evenout"  >
            <a class="col-md-12" style="font-size: 0.8em; font-weight: bold;" href="http://www.minedu.gov.gr/texniki-ekpaideusi-2/odigos-spoudon-gia-to-epal" target="_blank">Οδηγός Σπουδών για το ΕΠΑΛ </a>
        </li>
        </ul>


    <br>
    <br>
    <p align="left"><strong> Οδηγίες Ενημέρωσης ΓΕ.Λ. </strong></p>
        <ul class="list-group">
        <div *ngIf="hasvalue  == true">
        <li class="list-group-item isclickable evenout"  >
            <a class="col-md-12" style="font-size: 0.8em; font-weight: bold;"
            href="../pdfs/files/odigiessxoleiogel.pdf" target="_blank">Οδηγίες προς Διευθυντές ΓΕ.Λ. σχετικά με τα αποτελέσματα των Ηλεκτρονικών Δηλώσεων Προτίμησης</a>
        </li>
        </div>
        <li class="list-group-item isclickable oddout"  >
            <a class="col-md-12" style="font-size: 0.8em; font-weight: bold;" href="../pdfs/files/odigiesstudentgel.pdf" target="_blank">Οδηγίες ενημέρωσης μαθητών/μαθητριών σχετικά με το αποτέλεσμα της
Ηλεκτρονικής τους Δήλωσης Προτίμησης
για τα ΓΕ.Λ.</a>
        </li>
        </ul>

         <br>
    <br>
    <p align="left"><strong> Οδηγίες Ενημέρωσης ΕΠΑ.Λ. </strong></p>
        <ul class="list-group">

        <div *ngIf="hasvalue == true">
        <li class="list-group-item isclickable evenout"  >
            <a class="col-md-12" style="font-size: 0.8em; font-weight: bold;" href="../pdfs/files/MANUAL_epal_sxoleia.pdf" target="_blank">Οδηγίες προς Διευθυντές ΕΠΑ.Λ. σχετικά με τη Δήλωση της Δυναμικής της Σχολικής Μονάδας σε Αίθουσες</a>
        </li>
        <li class="list-group-item isclickable evenout"  >
            <a class="col-md-12" style="font-size: 0.8em; font-weight: bold;" href="../pdfs/files/odigiessxoleio.pdf" target="_blank">Οδηγίες προς Διευθυντές ΕΠΑ.Λ. σχετικά με τα αποτελέσματα των Ηλεκτρονικών Δηλώσεων Προτίμησης</a>
        </li>
        </div>
        <li class="list-group-item isclickable oddout"  >
            <a class="col-md-12" style="font-size: 0.8em; font-weight: bold;" href="../pdfs/files/odigiesstudent.pdf" target="_blank">Οδηγίες ενημέρωσης μαθητών/μαθητριών σχετικά με το αποτέλεσμα της
Ηλεκτρονικής τους Δήλωσης Προτίμησης
για τα ΕΠΑ.Λ.</a>
        </li>
        </ul>



        <div class="row" style="margin-top: 30px; margin-bottom: 30px;">
            <div class="col-md-6">
                <button type="button" class="btn-primary btn-lg pull-left isclickable" style="width: 9em;" (click)="goBack()" >
                    <span style="font-size: 0.9em; font-weight: bold;">Επιστροφή</span>
                </button>
            </div>
            <div class="col-md-6">
                <button type="button" class="btn-primary btn-lg pull-right isclickable" style="width: 9em;" (click)="goHome()" >
                    <span style="font-size: 0.9em; font-weight: bold;">Αρχική</span>
                </button>
            </div>
        </div>

   `
})

@Injectable() export default class LegalInfo {

    private authToken: string;
    private studentRole = STUDENT_ROLE;
    private authRole: string;
    private cuName: string;
    private loginInfo$: BehaviorSubject<ILoginInfoRecords>;
    private loginInfoSub: Subscription;
    private hasvalue: boolean;


    constructor(private router: Router,
                private loc: Location,
                private _ata: LoginInfoActions,
                private _ngRedux: NgRedux<IAppState>,


                ) {

        this.authToken = "";
        this.authRole = "";
        this.loginInfo$ = new BehaviorSubject(LOGININFO_INITIAL_STATE);
        this.hasvalue = false;


    }


    ngOnInit() {

        this.loginInfoSub = this._ngRedux.select("loginInfo")
            .map(loginInfo => <ILoginInfoRecords>loginInfo)
            .subscribe(loginInfo => {
                if (loginInfo.size > 0) {
                    loginInfo.reduce(({}, loginInfoObj) => {
                        this.authToken = loginInfoObj.auth_token;
                        this.authRole = loginInfoObj.auth_role;
                        return loginInfoObj;
                    }, {});

                    if (this.authRole === SCHOOL_ROLE || this.authRole === SCHOOLGEL_ROLE || this.authRole === SCHOOLGYM_ROLE) {
                        console.log(this.authRole,"role");
                        this.hasvalue = true;

                    }
                }
                this.loginInfo$.next(loginInfo);
            });

    }

    public goBack(): void {
        this.loc.back();

    }

    public goHome(): void {
        this.router.navigate([""]);
    }

}
