import { NgRedux } from "@angular-redux/store";
import { Component, OnDestroy, OnInit } from "@angular/core";
import { Injectable } from "@angular/core";
import { FormBuilder, FormGroup } from "@angular/forms";
import { Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";

import { LoginInfoActions } from "../../actions/logininfo.actions";
import { LOGININFO_INITIAL_STATE } from "../../store/logininfo/logininfo.initial-state";
import { ILoginInfoRecords } from "../../store/logininfo/logininfo.types";
import { IAppState } from "../../store/store";

@Component({
    selector: "intro-statement",
    template: `

    <div id="disclaimerNotice" (onHidden)="onHidden()" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header {{modalHeader | async}}" >
              <h3 class="modal-title pull-left"><i class="fa fa-check-square-o"></i>&nbsp;&nbsp;{{ modalTitle | async }}</h3>
            <button type="button" class="close pull-right" aria-label="Close" (click)="hideModal()">
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

    <div *ngIf="(loginInfo$ | async).size !== 0">


    <p align="left"><strong>Όροι και Προϋποθέσεις Συμμετοχής στην Ηλεκτρονική Υπηρεσία</strong></p>


    <p>
    Πριν προχωρήσετε στην υποβολή της Ηλεκτρονικής Αίτησης Εγγραφής - Δήλωσης Προτίμησης για την εγγραφή σας,
    παρακαλείσθε να διαβάσετε με προσοχή και να ενημερωθείτε για τις προϋποθέσεις και τις επιλογές που έχετε,
    προκειμένου να αποκτήσετε απολυτήριο τίτλο και πτυχίο ή μόνο πτυχίο της ειδικότητας που επιθυμείτε
    στα ΕΠΑΛ ή απολυτήριο τίτλο στα ΓΕΛ.
    </p>
    <p>
    Η Ηλεκτρονική Αίτηση Εγγραφής - Δήλωση Προτίμησης υπέχει θέση Υπ. Δήλωσης του ν. 1599/1986 (Α ́ 75).
    Δηλώνω υπεύθυνα ότι με τη συμμετοχή μου στην παρούσα διαδικασία αποδέχομαι πλήρως
    τους όρους και τις διαδικασίες εγγραφής στα δημόσια Λύκεια, έχω διαβάσει τις οδηγίες
    και δηλώνω την ορθότητα των προσωπικών μου στοιχείων, τα οποία θα καταχωρισθούν
    και θα τηρηθούν σύμφωνα με τις αρχές του κανονισμού προστασίας προσωπικών δεδομένων.
    </p>



  <form novalidate [formGroup]="formGroup" #form>
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

      <p align="left"><strong> Οδηγίες Ενημέρωσης</strong></p>
      <ul class="list-group">
        <li class="list-group-item isclickable oddout"  >
            <a class="col-md-12" style="font-size: 0.8em; font-weight: bold; " href="../pdfs/files/odigiesstudent.pdf"  target="_blank">
              Οδηγίες ενημέρωσης μαθητών/μαθητριών σχετικά με το αποτέλεσμα της
              Ηλεκτρονικής τους Αίτησης Εγγραφής - Δήλωσης Προτίμησης για τα ΕΠΑ.Λ.</a>
        </li>
        <li class="list-group-item isclickable oddout"  >
        <a class="col-md-12" style="font-size: 0.8em; font-weight: bold;" href="../pdfs/files/odigiesstudentgel.pdf" target="_blank">
          Οδηγίες ενημέρωσης μαθητών/μαθητριών σχετικά με το αποτέλεσμα της
          Ηλεκτρονικής τους Αίτησης Εγγραφής - Δήλωσης Προτίμησης για τα ΓΕ.Λ.</a>
        </li>
        </ul>

        <br>
        <p align="left"><strong> Προτεινόμενοι φυλλομετρητές  </strong></p>
        <ul class="list-group">
        <li class="list-group-item oddout"  >
            <a class="col-md-12" style="font-size: 0.8em; font-weight: bold; " target="_blank">
              Firefox (v.47 και πάνω), Chrome (v.49 και πάνω), IE (v.11 και πάνω) , Edge (v.13 και πάνω), Safari (v.6 και πάνω).</a>
        </li>
        </ul>


        <div class="row" style="margin-top: 20px;">
            <div class="col-md-1 ">
              <input type="checkbox" [checked]="disclaimerChecked | async"  formControlName="disclaimerChecked" >
            </div>
            <div class="col-md-9">
              <label for="disclaimerChecked">Διάβασα και αποδέχομαι τους παραπάνω όρους</label>
            </div>
        </div>

    </form>

         <div class="row" style="margin-top: 20px; margin-bottom: 20px;">
             <div class="col-md-6">
                 <button type="button" class="btn-primary btn-lg pull-left" (click)="navigateBack()">
                     <i class="fa fa-backward"></i>
                 </button>
             </div>
             <div class="col-md-6">
                 <button type="button" class="btn-primary btn-lg pull-right isclickable" style="width: 9em;" (click)="saveStatementAgree()">
                     <span style="font-size: 0.9em; font-weight: bold;">Συνέχεια&nbsp;&nbsp;&nbsp;</span><i class="fa fa-forward"></i>
                 </button>
             </div>

         </div>


      </div>



   `
})

@Injectable() export default class Disclaimer implements OnInit, OnDestroy {

    private formGroup: FormGroup;
    private loginInfo$: BehaviorSubject<ILoginInfoRecords>;
    private modalTitle: BehaviorSubject<string>;
    private modalText: BehaviorSubject<string>;
    private modalHeader: BehaviorSubject<string>;
    private disclaimerChecked: BehaviorSubject<number>;
    private loginInfoSub: Subscription;

    constructor(private fb: FormBuilder,
        private _ngRedux: NgRedux<IAppState>,
        private _lia: LoginInfoActions,
        private router: Router) {

        this.formGroup = this.fb.group({
            disclaimerChecked: ["", []],
        });

        this.loginInfo$ = new BehaviorSubject(LOGININFO_INITIAL_STATE);
        this.modalTitle = new BehaviorSubject("");
        this.modalText = new BehaviorSubject("");
        this.modalHeader = new BehaviorSubject("");
        this.disclaimerChecked = new BehaviorSubject(0);

    }

    public showModal(): void {
        (<any>$("#disclaimerNotice")).modal("show");
    }

    public hideModal(): void {
        (<any>$("#disclaimerNotice")).modal("hide");
    }

    public onHidden(): void {
        // this.isModalShown.next(false);
    }


    ngOnDestroy() {

        (<any>$("#disclaimerNotice")).remove();

        if (this.loginInfoSub)
            this.loginInfoSub.unsubscribe();
    }

    ngOnInit() {

        (<any>$("#disclaimerNotice")).appendTo("body");

        this.loginInfoSub = this._ngRedux.select("loginInfo")
            .map(loginInfo => <ILoginInfoRecords>loginInfo)
            .subscribe(linfo => {
                if (linfo.size > 0) {
                    linfo.reduce(({}, loginInfoObj) => {
                        this.formGroup.controls["disclaimerChecked"].setValue(loginInfoObj.disclaimer_checked);
                        this.disclaimerChecked.next(loginInfoObj.disclaimer_checked);
                        return loginInfoObj;
                    }, {});
                }
                this.loginInfo$.next(linfo);
            }, error => { console.log("error selecting loginInfo"); });
    }

    navigateBack() {
        this.router.navigate(["/parent-form"]);
    }

    saveStatementAgree() {
        if (!this.formGroup.controls["disclaimerChecked"].value) {
            this.modalHeader.next("modal-header-danger");
            this.modalTitle.next("Αποδοχή όρων χρήσης");
            this.modalText.next("Πρέπει να αποδεχθείτε πρώτα τους όρους χρήσης για να συνεχίσετε");
            this.showModal();
        } else {
            this._lia.saveStatementAgree(this.formGroup.controls["disclaimerChecked"].value);

            this.router.navigate(["school-type-select"]);
        }
    }
}
