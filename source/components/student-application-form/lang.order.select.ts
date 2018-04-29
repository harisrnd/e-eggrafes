import { NgRedux } from "@angular-redux/store";
import { Component, OnDestroy, OnInit } from "@angular/core";
import { Injectable } from "@angular/core";
import { Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";

import { LangCourseFieldsActions } from "../../actions/langcoursesfields.actions";
import { LANGCOURSE_FIELDS_INITIAL_STATE } from "../../store/langcoursesfields/langcoursesfields.initial-state";
import { ILangCourseFieldRecords, ILangCourseFieldRecord } from "../../store/langcoursesfields/langcoursesfields.types";
import { GelClassesActions } from "../../actions/gelclasses.actions";
import { IGelClass, IGelClassRecord, IGelClassRecords } from "../../store/gelclasses/gelclasses.types";

import { IAppState } from "../../store/store";

@Component({
    selector: "lang-order-select",
    template: `

    <div id="langcourseNotice" (onHidden)="onHidden()" class="modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
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
    <div class = "loading" *ngIf="(langcourseFields$ | async).length === 0">
    </div>

    <br/>

    <h4 *ngIf = "(selectedLangs$ | async).length !== 0"> Σειρά προτίμησης</h4>
    <p style="margin-top: 20px; line-height: 2em;" *ngIf = "(selectedLangs$ | async).length === 1" >Έχετε επιλέξει την παρακάτω Ξένη Γλώσσα. Εάν συμφωνείτε με την επιλογή σας
    πατήστε Συνέχεια, διαφορετικά τροποποιήστε τις επιλογές σας.</p>
    <p style="margin-top: 20px; line-height: 2em;" *ngIf = "(selectedLangs$ | async).length > 1" >
    Έχετε επιλέξει πρισσότερες από μία Ξένες Γλώσσες. Καθορίστε εδώ την επιθυμητη σειρά προτίμησης πατώντας τα αντίστοιχα βέλη δεξιά από τα ονόματα των Ξένων Γλωσσών.
    Αν συμφωνείτε με την υπάρχουσα σειρά προτίμησης, πατήστε <i>Συνέχεια</i>.</p>

    <ul class="list-group main-view" style="margin-top: 50px; margin-bottom: 50px;">
        <div *ngFor="let selectedLang$ of selectedLangs$ | async; let i = index; let isOdd=odd; let isEven=even">
            <li class="list-group-item "  [class.oddout]="isOdd" [class.evenout]="isEven">
                <span class="roundedNumber">{{(i+1)}}</span>&nbsp;&nbsp;{{selectedLang$.name}}
                  <i (click)="changeOrder(i)" *ngIf = "i !== 0" class="fa fa-arrow-circle-up isclickable pull-right" style="font-size: 1.5em;"></i>
            </li>
        </div>
    </ul>


    <div class="row" style="margin-top: 20px; margin-bottom: 20px;">
        <div class="col-md-6">
            <button type="button" class="btn-primary btn-lg pull-left isclickable" (click)="navigateBack();" >
                <i class="fa fa-backward"></i>
            </button>
        </div>
        <div class="col-md-6">
            <button type="button" class="btn-primary btn-lg pull-right isclickable" style="width: 9em;" (click)="navigateToElectiveCourseForm()" [disabled] = "numSelected === 0">
                <span style="font-size: 0.9em; font-weight: bold;">Συνέχεια&nbsp;&nbsp;&nbsp;</span><i class="fa fa-forward"></i>
            </button>
        </div>
    </div>

  `

})
@Injectable() export default class LangsOrderSelect implements OnInit, OnDestroy {

    private langcourseFields$: BehaviorSubject<ILangCourseFieldRecords>;
    private langcourseFieldsSub: Subscription;
    private gelclassesSub: Subscription;
    private selectedLangs$: BehaviorSubject<Array<ILangCourseFieldRecord>> = new BehaviorSubject(Array());
    private nonselectedLangs$: BehaviorSubject<Array<ILangCourseFieldRecord>> = new BehaviorSubject(Array());
    private modalTitle: BehaviorSubject<string>;
    private modalText: BehaviorSubject<string>;
    private modalHeader: BehaviorSubject<string>;
    public isModalShown: BehaviorSubject<boolean>;
    private langcourseSelected = <number>0;
    private activeClassId = -1;

    constructor(private _cfa: LangCourseFieldsActions,
        private _cfb: GelClassesActions,
        private _ngRedux: NgRedux<IAppState>,
        private router: Router) {
          this.langcourseFields$ = new BehaviorSubject(LANGCOURSE_FIELDS_INITIAL_STATE);

          this.modalTitle = new BehaviorSubject("");
          this.modalText = new BehaviorSubject("");
          this.modalHeader = new BehaviorSubject("");
          this.isModalShown = new BehaviorSubject(false);
    };


    ngOnInit() {

      (<any>$("#langcourseNotice")).appendTo("body");
      window.scrollTo(0, 0);

      this._cfb.getClassesList(false);
      this.gelclassesSub = this._ngRedux.select("gelclasses")
          .map(gelclasses => <IGelClassRecords>gelclasses)
          .subscribe(ecs => {
              if (ecs.size > 0) {
                   ecs.reduce(({}, gelclass) => {
                      if (gelclass.get("selected")===true ){
                          this.activeClassId = gelclass.get("id");
                      }
                      return gelclass;
                  }, {});
              }
          }, error => { console.log("error selecting gelclasses"); });

      this.langcourseFieldsSub = this._ngRedux.select("langcourseFields")
          .map(langcourseFields => <ILangCourseFieldRecords>langcourseFields)
          .subscribe(sfds => {
              this.langcourseSelected = 0;
              let selectedLangs = Array<ILangCourseFieldRecord>();
              let nonselectedLangs = Array<ILangCourseFieldRecord>();
              sfds.reduce(({}, langcourseField) => {
                  if (langcourseField.get("selected") === true) {
                      ++this.langcourseSelected;
                      selectedLangs.push(langcourseField.toJS());
                  }
                  else {
                      nonselectedLangs.push(langcourseField.toJS());
                  }

                  return langcourseField;
              }, {});
              this.langcourseFields$.next(sfds);

              selectedLangs.sort(this.compareLangs);
              for (let i = 0; i < selectedLangs.length; i++)
                  selectedLangs[i].order_id = i + 1;

              for (let i = 0; i < nonselectedLangs.length; i++)
                  nonselectedLangs[i].order_id = 0;

              this.selectedLangs$.next(selectedLangs);
              this.nonselectedLangs$.next(nonselectedLangs);

          }, error => { console.log("error selecting langcourseFields"); });

    }


    ngOnDestroy() {
        (<any>$("#langcourseNotice")).remove();
        if (this.langcourseFieldsSub) {
            this.langcourseFieldsSub.unsubscribe();
        }
    }

    public showModal(): void {
        (<any>$("#langcourseNotice")).modal("show");
    }

    public hideModal(): void {
        (<any>$("#langcourseNotice")).modal("hide");
    }

    public onHidden(): void {
        this.isModalShown.next(false);
    }


    compareLangs(a: ILangCourseFieldRecord, b: ILangCourseFieldRecord) {
        if (a.order_id < b.order_id)
            return -1;
        if (a.order_id > b.order_id)
            return 1;
        return 0;
    }

    compareLangsById(a: ILangCourseFieldRecord, b: ILangCourseFieldRecord) {
        if (a.id < b.id)
            return -1;
        if (a.id > b.id)
            return 1;
        return 0;
    }


    changeOrder(i) {
        let selectedLangs = Array<ILangCourseFieldRecord>();
        selectedLangs = this.selectedLangs$.getValue();
        [selectedLangs[i-1].order_id, selectedLangs[i].order_id] = [selectedLangs[i].order_id, selectedLangs[i-1].order_id];
        this.selectedLangs$.next(selectedLangs);
        this.updateStore();
      }


    updateStore() {

      let allLangs = Array<ILangCourseFieldRecord>();
      allLangs = this.selectedLangs$.getValue();
      let nonselectedLangs = Array<ILangCourseFieldRecord>();
      nonselectedLangs = this.nonselectedLangs$.getValue();
      allLangs = [ ...allLangs, ...nonselectedLangs];
      allLangs.sort(this.compareLangsById);

      this._cfa.saveLangsOrder(allLangs);
    }

    navigateToElectiveCourseForm() {
        if (this.langcourseSelected === 0) {
            this.modalTitle.next("Δεν επιλέχθηκε Ξένη Γλώσσα");
            this.modalText.next("Παρακαλούμε να επιλέξετε πρώτα μία Ξένη Γλώσσα");
            this.modalHeader.next("modal-header-danger");
            this.showModal();
        }
        else {
            this.updateStore();
            //this.router.navigate(["/gelstudent-application-form-main"]);
            this.router.navigate(["/electivecourse-fields-select"]);
        }

    }

    navigateBack() {
        this.updateStore();
        //if (this.activeClassId == 3)
        //  this.router.navigate(["/orientation-group-select"]);
        //else
          this.router.navigate(["/gel-class-select"]);
    }

}
