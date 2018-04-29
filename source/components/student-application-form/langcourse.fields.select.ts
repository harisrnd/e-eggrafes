import { NgRedux } from "@angular-redux/store";
import { Component, OnDestroy, OnInit } from "@angular/core";
import { Injectable } from "@angular/core";
import { Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";

import { LangCourseFieldsActions } from "../../actions/langcoursesfields.actions";
import { GelClassesActions } from "../../actions/gelclasses.actions";
import { LANGCOURSE_FIELDS_INITIAL_STATE } from "../../store/langcoursesfields/langcoursesfields.initial-state";
import { ILangCourseFieldRecords } from "../../store/langcoursesfields/langcoursesfields.types";
import { IGelClass, IGelClassRecord, IGelClassRecords } from "../../store/gelclasses/gelclasses.types";
import { IAppState } from "../../store/store";


@Component({
    selector: "langcourse-fields-select",
    template: `

    <div class="row">
             <breadcrumbs></breadcrumbs>
    </div>
    <h4> Επιλογή Ξένης Γλώσσας </h4>
     <div class = "loading" *ngIf="(langcourseFields$ | async).size === 0">
    </div>
       <p style="margin-top: 20px; line-height: 2em;"> Παρακαλώ επιλέξτε τη Ξένη Γλώσσα την οποία θα παρακολουθήσει ο μαθητής το νέο σχολικό έτος.
       Μπορείτε να επιλέξετε / απο-επιλέξετε περισσότερες προτιμήσεις, κάνοντας κλικ πάνω στην αντίστοιχη επιλογή.
       Σε περίπτωση περισσοτέρων της μίας επιλογής, βάλτε τις επιλογές σας σε επιθυμητή σειρά προτίμησης στην εμφανιζόμενη λίστα στο κάτω μέρος της οθόνης.
       Έπειτα επιλέξτε <i>Συνέχεια</i>.</p>
        <div class="list-group" *ngFor="let langcourseField$ of langcourseFields$ | async; let i=index;">
            <button *ngIf = "langcourseField$.selected === true" type="button" class="list-group-item list-group-item-action active" (click)="saveSelected(i, 1)" >{{langcourseField$.name}}</button>
            <button *ngIf = "langcourseField$.selected === false" type="button" class="list-group-item list-group-item-action" (click)="saveSelected(i, 0)" >{{langcourseField$.name}}</button>
    </div>

    <div>
      <lang-order-select></lang-order-select>
    </div>

`

})
@Injectable() export default class LangCourseFieldsSelect implements OnInit, OnDestroy {
    private langcourseFields$: BehaviorSubject<ILangCourseFieldRecords>;
    private langcourseFieldsSub: Subscription;
    private gelclassesSub: Subscription;

    constructor(private _cfa: LangCourseFieldsActions,
        private _cfb: GelClassesActions,
        private _ngRedux: NgRedux<IAppState>,
        private router: Router) {
        this.langcourseFields$ = new BehaviorSubject(LANGCOURSE_FIELDS_INITIAL_STATE);
    };

    ngOnInit() {

        /*
        this._cfa.getLangCourseFields(false);
        this.langcourseFieldsSub = this._ngRedux.select("langcourseFields")
            .map(langcourseFields => <ILangCourseFieldRecords>langcourseFields)
            .subscribe(sfds => {
                this.langcourseFields$.next(sfds);
            }, error => { console.log("error selecting langcourseFields"); });
        */

        window.scrollTo(0, 0);
        this.selectClass();

    }

    ngOnDestroy() {
        if (this.langcourseFieldsSub) this.langcourseFieldsSub.unsubscribe();
        if (this.gelclassesSub) this.gelclassesSub.unsubscribe();
    }


    selectClass() {

      this.gelclassesSub = this._ngRedux.select("gelclasses")
          .map(gelclasses => <IGelClassRecords>gelclasses)
          .subscribe(ecs => {
              if (ecs.size > 0) {
                   ecs.reduce(({}, gelclass) => {
                      if (gelclass.get("selected")===true ){
                          this.getAppropriateLangs(gelclass.get("id"));
                      }
                      return gelclass;
                  }, {});
              }
          }, error => { console.log("error selecting gelclasses"); });

    }

    getAppropriateLangs(classId) {

      this._cfa.getLangCourseFields(false, classId);
      this.langcourseFieldsSub = this._ngRedux.select("langcourseFields")
          .map(langcourseFields => <ILangCourseFieldRecords>langcourseFields)
          .subscribe(sfds => {
              this.langcourseFields$.next(sfds);
          }, error => { console.log("error selecting langcourseFields"); });

    }

    private saveSelected(ind: number, sel: number): void {
        this._cfa.saveLangCourseFieldsSelected(ind, sel, 0);
    }

    /*
    private saveSelected(ind: number, sel: number): void {
        this._cfa.saveLangCourseFieldsSelected(ind, sel, 0);
    }
    */


}
