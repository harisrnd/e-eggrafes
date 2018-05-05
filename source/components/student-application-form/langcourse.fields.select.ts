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

    <div id="numNotice" (onHidden)="onHidden()" class="modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header {{modalHeader | async}}">
              <h3 class="modal-title pull-left"><i class="fa fa-check-square-o"></i>&nbsp;&nbsp;{{ modalTitle | async }}</h3>
            <button type="button" class="close pull-right" aria-label="Close" (click)="hideModal()">
              <span aria-hidden="true"><i class="fa fa-times"></i></span>
            </button>
          </div>
          <div class="modal-body">
              <p>{{ modalText | async }}</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal" (click)="hideModal()">Κλείσιμο</button>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
             <breadcrumbs></breadcrumbs>
    </div>
    <h4> Επιλογή Ξένης Γλώσσας </h4>
     <div class = "loading" *ngIf="(langcourseFields$ | async).size === 0">
    </div>
       <p style="margin-top: 20px; line-height: 2em;">
       Επιλέξτε τη Ξένη Γλώσσα την οποία θα παρακολουθήσει ο/η μαθητής/τρια το νέο σχολικό έτος.
       Εμφανίζονται τρεις γλώσσες (Αγγλικά, Γαλλικά, Γερμανικά) αλλά πρέπει να <strong>επιλέξετε μια από τις γλώσσες που είχε</strong> ο/η μαθητής/τρια στο Γυμνάσιο.
       Μπορείτε να επιλέξετε / απο-επιλέξετε μια ή δύο προτιμήσεις, κάνοντας κλικ πάνω στην αντίστοιχη επιλογή.
       Σε περίπτωση δύο επιλογών, βάλτε τις επιλογές σας σε επιθυμητή σειρά προτίμησης στην εμφανιζόμενη λίστα στο κάτω μέρος της οθόνης.
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
    private numSelected: BehaviorSubject<number>;
    private modalTitle: BehaviorSubject<string>;
    private modalText: BehaviorSubject<string>;
    private modalHeader: BehaviorSubject<string>;
    public isModalShown: BehaviorSubject<boolean>;

    constructor(private _cfa: LangCourseFieldsActions,
        private _cfb: GelClassesActions,
        private _ngRedux: NgRedux<IAppState>,
        private router: Router) {

          this.langcourseFields$ = new BehaviorSubject(LANGCOURSE_FIELDS_INITIAL_STATE);
          this.numSelected = new BehaviorSubject(0);
          this.modalTitle = new BehaviorSubject("");
          this.modalText = new BehaviorSubject("");
          this.modalHeader = new BehaviorSubject("");
          this.isModalShown = new BehaviorSubject(false);
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

        (<any>$("#numNotice")).appendTo("body");
        window.scrollTo(0, 0);
        this.selectClass();

    }

    ngOnDestroy() {
        (<any>$("#numNotice")).remove();
        if (this.langcourseFieldsSub) this.langcourseFieldsSub.unsubscribe();
        if (this.gelclassesSub) this.gelclassesSub.unsubscribe();
    }

    public showModal(): void {
        (<any>$("#numNotice")).modal("show");
    }

    public hideModal(): void {
        (<any>$("#numNotice")).modal("hide");
    }

    public onHidden(): void {
        this.isModalShown.next(false);
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
              let numsel = 0;
              if (sfds.size > 0) {
                   sfds.reduce(({}, lang) => {
                      if (lang.get("selected") === true ){
                          ++numsel;
                      }
                      return lang;
                  }, {});
              }

              this.numSelected.next(numsel);
              //console.log("Trace value..");
              //console.log(this.numSelected.getValue());
              this.langcourseFields$.next(sfds);
          }, error => { console.log("error selecting langcourseFields"); });
    }

    private saveSelected(ind: number, sel: number): void {

        if (this.numSelected.getValue() === 2 && sel === 0)  {
          this.modalTitle.next("Μη δυνατότητα τρίτης επιλογής");
          this.modalText.next("Μπορείτε να κάνετε μέχρι δύο επιλογές. Απο-επιλέξετε κάποια από τις ήδη επιλεγμένες γλώσσες και προσπαθήστε ξανά.");
          this.modalHeader.next("modal-header-danger");
          this.showModal();

          return;
        }
        this._cfa.saveLangCourseFieldsSelected(ind, sel, 0);

    }

    /*
    private saveSelected(ind: number, sel: number): void {
        this._cfa.saveLangCourseFieldsSelected(ind, sel, 0);
    }
    */


}
