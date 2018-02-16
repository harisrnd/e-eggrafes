import { NgRedux } from "@angular-redux/store";
import { Component, OnDestroy, OnInit } from "@angular/core";
import { Injectable } from "@angular/core";
import { Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";

import { ElectiveCourseFieldsActions } from "../../actions/electivecoursesfields.actions";
import { ELECTIVECOURSE_FIELDS_INITIAL_STATE } from "../../store/electivecoursesfields/electivecoursesfields.initial-state";
import { IElectiveCourseFieldRecords, IElectiveCourseFieldRecord } from "../../store/electivecoursesfields/electivecoursesfields.types";
import { GelClassesActions } from "../../actions/gelclasses.actions";
import { IGelClass, IGelClassRecord, IGelClassRecords } from "../../store/gelclasses/gelclasses.types";

import { IAppState } from "../../store/store";

@Component({
    selector: "course-order-select",
    template: `

    <div id="electivecourseNotice" (onHidden)="onHidden()" class="modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
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
    <div class = "loading" *ngIf="(electivecourseFields$ | async).length === 0">
    </div>

    <br/>

    <h4 *ngIf = "(selectedCourses$ | async).length !== 0"> Σειρά προτίμησης</h4>
    <p style="margin-top: 20px; line-height: 2em;" *ngIf = "(selectedCourses$ | async).length === 1" >Έχετε επιλέξει το παρακάτω μάθημα επιλογής. Εάν συμφωνείτε με την επιλογή σας
    πατήστε Συνέχεια, διαφορετικά τροποποιήστε τις επιλογές σας.</p>
    <p style="margin-top: 20px; line-height: 2em;" *ngIf = "(selectedCourses$ | async).length > 1" >
    Έχετε επιλέξει πρισσότερα από ένα μαθήματα. Καθορίστε εδώ την επιθυμητη σειρά προτίμησης πατώντας τα αντίστοιχα βέλη δεξιά από τα ονόματα των μαθημάτων.
    Αν συμφωνείτε με την υπάρχουσα σειρά προτίμησης, πατήστε <i>Συνέχεια</i>.</p>

    <ul class="list-group main-view" style="margin-top: 50px; margin-bottom: 50px;">
        <div *ngFor="let selectedCourse$ of selectedCourses$ | async; let i = index; let isOdd=odd; let isEven=even">
            <li class="list-group-item "  [class.oddout]="isOdd" [class.evenout]="isEven">
                <span class="roundedNumber">{{(i+1)}}</span>&nbsp;&nbsp;{{selectedCourse$.name}}
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
            <button type="button" class="btn-primary btn-lg pull-right isclickable" style="width: 9em;" (click)="navigateToStudentForm()" [disabled] = "numSelected === 0">
                <span style="font-size: 0.9em; font-weight: bold;">Συνέχεια&nbsp;&nbsp;&nbsp;</span><i class="fa fa-forward"></i>
            </button>
        </div>
    </div>

  `

})
@Injectable() export default class CoursesOrderSelect implements OnInit, OnDestroy {

    private electivecourseFields$: BehaviorSubject<IElectiveCourseFieldRecords>;
    private electivecourseFieldsSub: Subscription;
    private gelclassesSub: Subscription;
    private selectedCourses$: BehaviorSubject<Array<IElectiveCourseFieldRecord>> = new BehaviorSubject(Array());
    private nonselectedCourses$: BehaviorSubject<Array<IElectiveCourseFieldRecord>> = new BehaviorSubject(Array());
    private modalTitle: BehaviorSubject<string>;
    private modalText: BehaviorSubject<string>;
    private modalHeader: BehaviorSubject<string>;
    public isModalShown: BehaviorSubject<boolean>;
    private electivecourseSelected = <number>0;
    private activeClassId = -1;

    constructor(private _cfa: ElectiveCourseFieldsActions,
        private _cfb: GelClassesActions,
        private _ngRedux: NgRedux<IAppState>,
        private router: Router) {
          this.electivecourseFields$ = new BehaviorSubject(ELECTIVECOURSE_FIELDS_INITIAL_STATE);

          this.modalTitle = new BehaviorSubject("");
          this.modalText = new BehaviorSubject("");
          this.modalHeader = new BehaviorSubject("");
          this.isModalShown = new BehaviorSubject(false);
    };


    ngOnInit() {

      (<any>$("#electivecourseNotice")).appendTo("body");
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

      this.electivecourseFieldsSub = this._ngRedux.select("electivecourseFields")
          .map(electivecourseFields => <IElectiveCourseFieldRecords>electivecourseFields)
          .subscribe(sfds => {
              this.electivecourseSelected = 0;
              let selectedCourses = Array<IElectiveCourseFieldRecord>();
              let nonselectedCourses = Array<IElectiveCourseFieldRecord>();
              sfds.reduce(({}, electivecourseField) => {
                  if (electivecourseField.get("selected") === true) {
                      ++this.electivecourseSelected;
                      selectedCourses.push(electivecourseField.toJS());
                  }
                  else {
                      nonselectedCourses.push(electivecourseField.toJS());
                  }

                  return electivecourseField;
              }, {});
              this.electivecourseFields$.next(sfds);

              selectedCourses.sort(this.compareCourses);
              for (let i = 0; i < selectedCourses.length; i++)
                  selectedCourses[i].order_id = i + 1;

              for (let i = 0; i < nonselectedCourses.length; i++)
                  nonselectedCourses[i].order_id = 0;

              this.selectedCourses$.next(selectedCourses);
              this.nonselectedCourses$.next(nonselectedCourses);

          }, error => { console.log("error selecting electivecourseFields"); });

    }


    ngOnDestroy() {
        (<any>$("#electivecourseNotice")).remove();
        if (this.electivecourseFieldsSub) {
            this.electivecourseFieldsSub.unsubscribe();
        }
    }

    public showModal(): void {
        (<any>$("#electivecourseNotice")).modal("show");
    }

    public hideModal(): void {
        (<any>$("#electivecourseNotice")).modal("hide");
    }

    public onHidden(): void {
        this.isModalShown.next(false);
    }


    compareCourses(a: IElectiveCourseFieldRecord, b: IElectiveCourseFieldRecord) {
        if (a.order_id < b.order_id)
            return -1;
        if (a.order_id > b.order_id)
            return 1;
        return 0;
    }

    compareCoursesById(a: IElectiveCourseFieldRecord, b: IElectiveCourseFieldRecord) {
        if (a.id < b.id)
            return -1;
        if (a.id > b.id)
            return 1;
        return 0;
    }


    changeOrder(i) {
        let selectedCourses = Array<IElectiveCourseFieldRecord>();
        selectedCourses = this.selectedCourses$.getValue();
        [selectedCourses[i-1].order_id, selectedCourses[i].order_id] = [selectedCourses[i].order_id, selectedCourses[i-1].order_id];
        this.selectedCourses$.next(selectedCourses);
        this.updateStore();
      }


    updateStore() {

      let allCourses = Array<IElectiveCourseFieldRecord>();
      allCourses = this.selectedCourses$.getValue();
      let nonselectedCourses = Array<IElectiveCourseFieldRecord>();
      nonselectedCourses = this.nonselectedCourses$.getValue();
      allCourses = [ ...allCourses, ...nonselectedCourses];
      allCourses.sort(this.compareCoursesById);

      this._cfa.saveCoursesOrder(allCourses);
    }

    navigateToStudentForm() {
        if (this.electivecourseSelected === 0) {
            this.modalTitle.next("Δεν επιλέχθηκε μάθημα επιλογής");
            this.modalText.next("Παρακαλούμε να επιλέξετε πρώτα ένα μάθημα επιλογής");
            this.modalHeader.next("modal-header-danger");
            this.showModal();
        }
        else {
            this.updateStore();
            this.router.navigate(["/gelstudent-application-form-main"]);
        }

    }

    navigateBack() {
        this.updateStore();
        if (this.activeClassId == 3)
          this.router.navigate(["/orientation-group-select"]);
        else
          this.router.navigate(["/gel-class-select"]);
    }

}
