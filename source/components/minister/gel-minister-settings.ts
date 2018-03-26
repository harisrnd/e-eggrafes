import { NgRedux } from "@angular-redux/store";
import { Component, OnDestroy, OnInit } from "@angular/core";
import { Injectable } from "@angular/core";
import { FormBuilder, FormGroup, Validators } from "@angular/forms";
import { Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";
import { IMyDpOptions } from "mydatepicker";

import { HelperDataService } from "../../services/helper-data-service";
import { LOGININFO_INITIAL_STATE } from "../../store/logininfo/logininfo.initial-state";
import { ILoginInfoRecords } from "../../store/logininfo/logininfo.types";
import { IAppState } from "../../store/store";

@Component({
    selector: "gel-minister-settings",
    template: `

    <div
      class = "loading" *ngIf="(dataRetrieved == -1  || (showLoader | async) === true)" >
    </div>

    <div id="configNotice" (onHidden)="onHidden()" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
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

        <form novalidate [formGroup]="formGroup"  #form>
          <h5> >Ρυθμίσεις <br><br></h5>
          <div class="row">
            <div class="col-md-1 ">
              <input type="checkbox" [checked]="capacityDisabled"  formControlName="capacityDisabled"
              (click)="toggleCapacityFilter()" >
            </div>
            <div class="col-md-9">
              <label for="capacityDisabled">Απενεργοποίηση δυνατότητας τροποποίησης χωρητικότητας από τους Διευθυντές σχολείων</label>
            </div>
          </div>
          <br>

          <div class="row">
            <div class="col-md-1 ">
              <input type="checkbox" [checked]="directorViewDisabled" formControlName="directorViewDisabled"
              (click)="toggleDirectorView()" >
            </div>
            <div class="col-md-9">
              <label for="directorViewDisabled">Απενεργοποίηση δυνατότητας προβολής κατανομής μαθητών από τους Διευθυντές σχολείων</label>
            </div>
          </div>
          <br>

          <div class="row">
            <div class="col-md-1 ">
              <input type="checkbox" [checked]="applicantsLoginDisabled" formControlName="applicantsLoginDisabled"
              (click)="toggleApplicantsLogin()" >
            </div>
            <div class="col-md-9">
              <label for="applicantsLoginDisabled">Απενεργοποίηση δυνατότητας υποβολής δήλωσης προτίμησης</label>
            </div>
          </div>
          <br>

          <div class="row">
            <div class="col-md-1 ">
              <input type="checkbox" [checked]="applicantsAppModifyDisabled" formControlName="applicantsAppModifyDisabled"
              (click)="toggleApplicantsAppModify()" >
            </div>
            <div class="col-md-9">
              <label for="applicantsAppModifyDisabled">Απενεργοποίηση δυνατότητας τροποποίησης αίτησης </label>
            </div>
          </div>
          <br>

          <div class="row">
            <div class="col-md-1 ">
              <input type="checkbox" [checked]="applicantsAppDeleteDisabled" formControlName="applicantsAppDeleteDisabled"
              (click)="toggleApplicantsAppDelete()" >
            </div>
            <div class="col-md-9">
              <label for="applicantsAppDeleteDisabled">Απενεργοποίηση δυνατότητας διαγραφής αίτησης </label>
            </div>
          </div>
          <br>

          <div class="row">
            <div class="col-md-1 ">
              <input type="checkbox" [checked]="applicantsResultsDisabled" formControlName="applicantsResultsDisabled"
              (click)="toggleApplicantsResults()" >
            </div>
            <div class="col-md-9">
              <label for="applicantsResultsDisabled">Απενεργοποίηση δυνατότητας προβολής αποτελεσμάτων κατανομής από τους μαθητές </label>
            </div>
          </div>
          <br>

          <div class="row">
            <div class="col-md-1 ">
              <input type="checkbox" [checked]="secondPeriodEnabled" formControlName="secondPeriodEnabled"
              (click)="toggleSecondPeriod()" >
            </div>
            <div class="col-md-9">
              <label for="secondPeriodEnabled">Ενεργοποίηση δεύτερης περιόδου αιτήσεων </label>
            </div>
          </div>
          <br>

          <div class="row" *ngIf = "secondPeriodEnabled">
            <div class="col-md-1 "></div>
            <div class="col-md-9 ">
              <label for="distributionstartdate">Ημερομηνία εκκίνησης υποπεριόδου δεύτερης περιόδου αιτήσεων(<span style="color: #ff0000;">*</span>)</label>
            </div>
          </div>
          <div class="row" *ngIf = "secondPeriodEnabled">
            <div class="col-md-1 "></div>
            <div class="col-md-9 ">
              <my-date-picker name="distributionstartdate" [options]="myDatePickerOptions" formControlName="distributionstartdate" locale="el"></my-date-picker>
              <div class="alert alert-danger" *ngIf="formGroup.get('distributionstartdate').touched && formGroup.get('distributionstartdate').hasError('required')">
                  Συμπληρώστε την ημερομηνία εκκίνησης!
              </div>
            </div>
          </div>
           <div class="row">
            <div class="col-md-1 ">
              <input type="checkbox" [checked]="smallClassApproved"  formControlName="smallClassApproved"
              (click)="toggleSmallClassesFilter()" >
            </div>
            <div class="col-md-9">
              <label for="smallClassApproved">Ενεργοποίηση μη διαθεσιμότητας μη εγκεκριμένων τμημάτων</label>
            </div>
          </div>
          <br>

          <div class="row">
           <div class="col-md-1 ">
             <input type="checkbox" [checked]="wsIdentEnabled"  formControlName="wsIdentEnabled"
             (click)="toggleWsIdent()" >
           </div>
           <div class="col-md-9">
             <label for="wsIdentEnabled">Ενεργοποίηση Web Service Ταυτοποίησης Μαθητή του ΠΣ Myschool</label>
           </div>
         </div>
         <br>

         <div class="row">
          <div class="col-md-1 ">
            <input type="checkbox" [checked]="gsisIdentEnabled"  formControlName="gsisIdentEnabled"
            (click)="toggleGsisIdent()" >
          </div>
          <div class="col-md-9">
            <label for="gsisIdentEnabled">Ενεργοποίηση χρήσης δεδομένων από τη ΓΓΠΣ</label>
          </div>
        </div>
          <br>

          <button type="submit" class="btn btn-md pull-right"  (click)="storeSettings()" [disabled] = "secondPeriodEnabled && formGroup.get('distributionstartdate').hasError('required')" >
              Εφαρμογή
          </button>

        </form>

      </div>

   `
})

@Injectable() export default class GelMinisterSettings implements OnInit, OnDestroy {

    private formGroup: FormGroup;
    private loginInfo$: BehaviorSubject<ILoginInfoRecords>;
    private modalTitle: BehaviorSubject<string>;
    private modalText: BehaviorSubject<string>;
    private modalHeader: BehaviorSubject<string>;
    private settings$: BehaviorSubject<any>;
    private OffLineCalculation$: BehaviorSubject<any>;
    private OffLineCalculationSub: Subscription;
    private loginInfoSub: Subscription;
    private settingsSub: Subscription;
    private capacityDisabled: boolean;
    private directorViewDisabled: boolean;
    private applicantsLoginDisabled: boolean;
    private applicantsAppModifyDisabled: boolean;
    private applicantsAppDeleteDisabled: boolean;
    private applicantsResultsDisabled: boolean;
    private secondPeriodEnabled: boolean;
    private dateStartBPeriod: string;
    private dataRetrieved: number;
    private smallClassApproved: boolean;
    private wsIdentEnabled: boolean;
    private gsisIdentEnabled: boolean;
    private showLoader: BehaviorSubject<boolean>;

    private minedu_userName: string;
    private minedu_userPassword: string;

    private myDatePickerOptions: IMyDpOptions = {
        // other options...
        sunHighlight: false,
        editableDateField: false,
        dateFormat: "dd/mm/yyyy",
    };

    constructor(private fb: FormBuilder,
        private _ngRedux: NgRedux<IAppState>,
        private _hds: HelperDataService,
        private router: Router) {

        this.formGroup = this.fb.group({
            capacityDisabled: ["", []],
            directorViewDisabled: ["", []],
            applicantsLoginDisabled: ["", []],
            applicantsAppModifyDisabled: ["", []],
            applicantsAppDeleteDisabled: ["", []],
            applicantsResultsDisabled: ["", []],
            secondPeriodEnabled: ["", []],
            distributionstartdate:["", [Validators.required]],
            smallClassApproved:["",[]],
            wsIdentEnabled:["",[]],
            gsisIdentEnabled:["",[]],
        });

        this.formGroup.get("smallClassApproved").disable();
        this.formGroup.get("capacityDisabled").disable();

        this.loginInfo$ = new BehaviorSubject(LOGININFO_INITIAL_STATE);
        this.settings$ = new BehaviorSubject([{}]);
        this.modalTitle = new BehaviorSubject("");
        this.modalText = new BehaviorSubject("");
        this.modalHeader = new BehaviorSubject("");
        this.OffLineCalculation$ = new BehaviorSubject([{}]);
        this.showLoader = new BehaviorSubject(false);

    }

    public showModal(): void {
        (<any>$("#configNotice")).modal("show");
    }

    public hideModal(): void {
        (<any>$("#configNotice")).modal("hide");
    }

    public onHidden(): void {
        // this.isModalShown.next(false);
    }


    ngOnDestroy() {

        (<any>$("#configNotice")).remove();

        if (this.loginInfoSub)
            this.loginInfoSub.unsubscribe();
        if (this.settingsSub)
            this.settingsSub.unsubscribe();
    }

    ngOnInit() {

        console.log("YESSSSS");

        (<any>$("#configNotice")).appendTo("body");

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

        this.retrieveSettings();

    }

    retrieveSettings() {

        this.dataRetrieved = -1;

        this.settingsSub = this._hds.retrieveAdminSettings("gel", this.minedu_userName, this.minedu_userPassword).subscribe(data => {
            this.settings$.next(data);

            this.capacityDisabled = Boolean(Number(this.settings$.value["capacityDisabled"]));
            this.directorViewDisabled = Boolean(Number(this.settings$.value["directorViewDisabled"]));
            this.applicantsLoginDisabled = Boolean(Number(this.settings$.value["applicantsLoginDisabled"]));
            this.applicantsAppModifyDisabled = Boolean(Number(this.settings$.value["applicantsAppModifyDisabled"]));
            this.applicantsAppDeleteDisabled = Boolean(Number(this.settings$.value["applicantsAppDeleteDisabled"]));
            this.applicantsResultsDisabled = Boolean(Number(this.settings$.value["applicantsResultsDisabled"]));
            this.secondPeriodEnabled = Boolean(Number(this.settings$.value["secondPeriodEnabled"]));
            this.dateStartBPeriod = this.settings$.value["dateStart"];
            if (this.dateStartBPeriod != '')  {
              let dateparts = this.dateStartBPeriod.split("-",3);
              this.formGroup.controls["distributionstartdate"].setValue( {date: {year: Number(dateparts[2]), month: Number(dateparts[1]), day: Number(dateparts[0])}} );
            }
            //this.formGroup.controls["distributionstartdate"].setValue(this.populateDate(this.dateStartBPeriod));
            this.smallClassApproved = Boolean(Number(this.settings$.value["smallClassApproved"]));
            this.wsIdentEnabled = Boolean(Number(this.settings$.value["wsIdentEnabled"]));
            this.gsisIdentEnabled = Boolean(Number(this.settings$.value["gsisIdentEnabled"]));

            this.dataRetrieved = 1;
        },
            error => {
                this.settings$.next([{}]);
                this.dataRetrieved = 0;
                console.log("Error Getting MinisterRetrieveSettings");
            });
    }

    storeSettings() {

        this.dataRetrieved = -1;

        if (this.secondPeriodEnabled) {
          this.dateStartBPeriod = this.formGroup.controls["distributionstartdate"].value.date.day + "-" +
              this.formGroup.controls["distributionstartdate"].value.date.month + "-" +
              this.formGroup.controls["distributionstartdate"].value.date.year;
        }
        else
            this.dateStartBPeriod = "0-0-0000";


        this.settingsSub = this._hds.storeAdminSettings("gel", this.minedu_userName, this.minedu_userPassword,
            this.capacityDisabled, this.directorViewDisabled, this.applicantsLoginDisabled,
            this.applicantsAppModifyDisabled, this.applicantsAppDeleteDisabled, this.applicantsResultsDisabled,
            this.secondPeriodEnabled, this.dateStartBPeriod, this.smallClassApproved, this.wsIdentEnabled, this.gsisIdentEnabled)
            .subscribe(data => {
                this.settings$.next(data);
                this.dataRetrieved = 1;

                this.modalTitle.next("Ρύθμιση Παραμέτρων");
                this.modalText.next("Έγινε εφαρμογή των νέων σας ρυθμίσεων.");
                this.modalHeader.next("modal-header-success");
                this.showModal();
            },
            error => {
                this.settings$.next([{}]);
                this.dataRetrieved = 0;
                console.log("Error Getting MinisterStoreSettings");

                this.modalTitle.next("Ρύθμιση Παραμέτρων");
                this.modalText.next("ΑΠΟΤΥΧΙΑ εφαρμογής των νέων σας ρυθμίσεων.");
                this.modalHeader.next("modal-header-danger");
                this.showModal();
            });
    }

    toggleCapacityFilter() {
        this.capacityDisabled = !this.capacityDisabled;
    }

    toggleDirectorView() {
        this.directorViewDisabled = !this.directorViewDisabled;
    }

    toggleApplicantsLogin() {
        this.applicantsLoginDisabled = !this.applicantsLoginDisabled;
    }

    toggleApplicantsAppModify() {
        this.applicantsAppModifyDisabled = !this.applicantsAppModifyDisabled;
    }

    toggleApplicantsAppDelete() {
        this.applicantsAppDeleteDisabled = !this.applicantsAppDeleteDisabled;
    }

    toggleApplicantsResults() {
        this.applicantsResultsDisabled = !this.applicantsResultsDisabled;
    }

    toggleSecondPeriod() {
        this.secondPeriodEnabled = !this.secondPeriodEnabled;
    }

    toggleSmallClassesFilter(){

       if (this.smallClassApproved == false)
       {

         this.showLoader.next(true);
         this.OffLineCalculationSub = this._hds.OffLinecalculationofSmallClasses(this.minedu_userName, this.minedu_userPassword)
                  .subscribe(data => {
                      this.showLoader.next(false);
                      this.OffLineCalculation$.next(data);
                  },
                  error => {
                      this.showLoader.next(false);
                      console.log("Error for the offlineCalculation");
                  });
       }

       this.smallClassApproved =!this.smallClassApproved;
    }

    toggleWsIdent(){
       this.wsIdentEnabled =!this.wsIdentEnabled;
    }

    toggleGsisIdent(){
       this.gsisIdentEnabled =!this.gsisIdentEnabled;
    }


}
