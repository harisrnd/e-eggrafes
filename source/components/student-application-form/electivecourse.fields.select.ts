import { NgRedux } from "@angular-redux/store";
import { Component, OnDestroy, OnInit } from "@angular/core";
import { Injectable } from "@angular/core";
import { Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";

import { ElectiveCourseFieldsActions } from "../../actions/electivecoursesfields.actions";
import { ELECTIVECOURSE_FIELDS_INITIAL_STATE } from "../../store/electivecoursesfields/electivecoursesfields.initial-state";
import { IElectiveCourseFieldRecords } from "../../store/electivecoursesfields/electivecoursesfields.types";
import { GelClassesActions } from "../../actions/gelclasses.actions";
//import { GELCLASSES_INITIAL_STATE } from "../../store/gelclasses/gelclasses.initial-state";
import { IGelClass, IGelClassRecord, IGelClassRecords } from "../../store/gelclasses/gelclasses.types";
import { IAppState } from "../../store/store";


@Component({
    selector: "electivecourse-fields-select",
    template: `

    <div class="row">
             <breadcrumbs></breadcrumbs>
    </div>
    <h4> Επιλογή Μαθήματος Επιλογής </h4>
    <div class = "loading" *ngIf=" !(electivecourseFields$ | async) || (electivecourseFields$ | async).size === 0"></div>

    <p style="margin-top: 20px; line-height: 2em;"> Παρακαλώ επιλέξτε το μάθημα επιλογής το οποίο θα παρακολουθήσει ο μαθητής το νέο σχολικό έτος.
       Μπορείτε να επιλέξετε / απο-επιλέξετε περισσότερες προτιμήσεις, κάνοντας κλικ πάνω στην αντίστοιχη επιλογή.
       Σε περίπτωση περισσοτέρων της μίας επιλογής, βάλτε τις επιλογές σας σε επιθυμητή σειρά προτίμησης στην εμφανιζόμενη λίστα στο κάτω μέρος της οθόνης.
       Έπειτα επιλέξτε <i>Συνέχεια</i>.</p>
        <div class="list-group" *ngFor="let electivecourseField$ of electivecourseFields$ | async; let i=index;">
            <button *ngIf = "electivecourseField$.selected === true" type="button" class="list-group-item list-group-item-action active" (click)="saveSelected(i, 1)" >{{electivecourseField$.name}}</button>
            <button *ngIf = "electivecourseField$.selected === false" type="button" class="list-group-item list-group-item-action" (click)="saveSelected(i, 0)" >{{electivecourseField$.name}}</button>
        </div>
    <div>
      <course-order-select></course-order-select>
    </div>

`

})
@Injectable() export default class ElectiveCourseFieldsSelect implements OnInit, OnDestroy {
    private electivecourseFields$: BehaviorSubject<IElectiveCourseFieldRecords>;
    private electivecourseFieldsSub: Subscription;
    private gelclassesSub: Subscription;
    //private activeClassId: number;

    constructor(private _cfa: ElectiveCourseFieldsActions,
        private _cfb: GelClassesActions,
        private _ngRedux: NgRedux<IAppState>,
        private router: Router) {
        this.electivecourseFields$ = new BehaviorSubject(ELECTIVECOURSE_FIELDS_INITIAL_STATE);
    };

    ngOnInit() {

      //this.activeClassId = -1;
      window.scrollTo(0, 0);
      this.selectClass();

    }

    ngOnDestroy() {
        if (this.electivecourseFieldsSub) this.electivecourseFieldsSub.unsubscribe();
        if (this.gelclassesSub) this.gelclassesSub.unsubscribe();
    }

    selectClass() {

      //this._cfb.getClassesList(false);

      this.gelclassesSub = this._ngRedux.select("gelclasses")
          .map(gelclasses => <IGelClassRecords>gelclasses)
          .subscribe(ecs => {
              if (ecs.size > 0) {
                   ecs.reduce(({}, gelclass) => {
                      if (gelclass.get("selected")===true ){
                          //this.activeClassId = gelclass.get("id");
                          this.getAppropriateCourses(gelclass.get("id"));
                      }
                      return gelclass;
                  }, {});
              }
          }, error => { console.log("error selecting gelclasses"); });

    }

    getAppropriateCourses(classId) {

      this._cfa.getElectiveCourseFields(false, /*this.activeClassId*/ classId);
      this.electivecourseFieldsSub = this._ngRedux.select("electivecourseFields")
          .map(electivecourseFields => <IElectiveCourseFieldRecords>electivecourseFields)
          .subscribe(sfds => {
              //sfds.reduce(({}, electivecourseField) => {
                  //return electivecourseField;
              //}, {});
              this.electivecourseFields$.next(sfds);
          }, error => { console.log("error selecting electivecourseFields"); });

    }

    private saveSelected(ind: number, sel: number): void {
        this._cfa.saveElectiveCourseFieldsSelected(ind, sel, 0);
    }


}
