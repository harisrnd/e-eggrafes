import { Component, OnDestroy, OnInit } from "@angular/core";
import { Injectable } from "@angular/core";
import { ActivatedRoute, Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";

import { HelperDataService } from "../../services/helper-data-service";

@Component({
    selector: "directorgym-view",
    template: `

 <div id="applicationDeleteConfirm" (onHidden)="onHidden()" class="modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header modal-header-danger">
              <h3 class="modal-title pull-left"><i class="fa fa-close"></i>&nbsp;&nbsp;Διαγραφή Δήλωσης Προτίμησης ΕΠΑΛ</h3>
            <button type="button" class="close pull-right" aria-label="Close" (click)="hideModal()">
              <span aria-hidden="true"><i class="fa fa-times"></i></span>
            </button>
          </div>
          <div class="modal-body">
              <p>Επιλέξατε να διαγράψετε τη δήλωση προτίμησης ΓΕΛ. Παρακαλούμε επιλέξτε Επιβεβαίωση</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal" (click)="hideConfirmModal()">Ακύρωση</button>
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal" (click)="deleteApplicationDo()">Επιβεβαίωση</button>
          </div>
        </div>
      </div>
    </div>

    <div id="applicationDeleteError" (onHidden)="onHidden()" class="modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header modal-header-danger">
              <h3 class="modal-title pull-left"><i class="fa fa-ban"></i>&nbsp;&nbsp;Αποτυχία Διαγραφής Δήλωσης Προτίμησης </h3>
            <button type="button" class="close pull-right" aria-label="Close" (click)="hideModal()">
              <span aria-hidden="true"><i class="fa fa-times"></i></span>
            </button>
          </div>
          <div class="modal-body">
              <p>Η δήλωσή δεν διαγράφηκε. Δεν μπορείτε να διαγράψετε τη δήλωσή μαθητή εαν έχετε κάνει την επιβεβαίωση εγγραφής.
              </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-right" data-dismiss="modal" (click)="hideErrorModal()">Κλείσιμο</button>
          </div>
        </div>
      </div>
    </div>

    <div class = "loading" *ngIf="(showLoader | async) === true"></div>
    <div style="min-height: 500px;">
    <form>
       <p style="margin-top: 20px; line-height: 2em;"> H παρακάτω λίστα περιλαμβάνει τους μαθητές της Γ 'ταξης του σχολείου σας οι οποίοι απολύθηκαν και καταχωρησαν την αιτηση δήλωση προτιμησης για εγγραφή σε ΓΕΛ/ΕΠΑΛ. </p>
       <p style="margin-top: 20px; line-height: 2em;"> Παρακαλείστε να ελέγξετε και να επικοινωνησετε με την οικέια Διευθυνση Δευτεροβάθμιας ΕΚπαίδευσης σε περιπτωση που εντοπίσετε ελλείψεις. </p>

      <div class="row">
         <div class="col-md-6" style="font-weight: bold;"> Ονοματεπώνυμο Μαθητή.</div>
         <div class="col-md-3" style="font-weight: bold;"> Διέυθυνση Κατοικίας Μαθητή.</div>
         <div class="col-md-3" style="font-weight: bold;"> <span class="pull-right" style="text-align: right; padding-right: 2px;">Λύκειο Εγγραφής Μαθητή</span></div>
      </div>
      <div *ngFor="let StudentDetails$  of StudentInfo$ | async; let i=index; let isOdd=odd; let isEven=even" >
                <li class="list-group-item isclickable" (click)="setActive(i)" [class.oddout]="isOdd" [class.evenout]="isEven">
                <div class="row"  style="line-height: 2em;">
                  <div class="col-md-6" style="font-weight: bold;" >{{StudentDetails$.name}} {{StudentDetails$.studentsurname}}</div>
                  <div class="col-md-3" style="font-weight: bold;" >{{StudentDetails$.regionaddress}}, {{StudentDetails$.regiontk}} - {{StudentDetails$.regionarea}}</div>
                  <div class="col-md-3" style="font-weight: bold;" ><span class="pull-right" style="text-align: right; padding-right: 2px;">{{StudentDetails$.gel}}</span></div>
                </div>
                </li>
       </div>
      </form>
      </div>


  <div id="checksaved" (onHidden)="onHidden('#checksaved')"
    class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header modal-header-success">
            <h3 class="modal-title pull-left"><i class="fa fa-check-square-o"></i>&nbsp;&nbsp;Η επιλογή σας έχει αποθηκευτεί</h3>
            <button type="button" class="close pull-right" aria-label="Close" (click)="hideModal('#checksaved')">
              <span aria-hidden="true"><i class="fa fa-times"></i></span>
            </button>
          </div>
          <div class="modal-body">
            <p>Η επιλογή σας έχει αποθηκευτεί</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Κλείσιμο</button>
          </div>
        </div>
      </div>
    </div>


<div id="dangermodal" (onHidden)="onHidden('#dangermodal')"
    class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header modal-header-danger">
            <h3 class="modal-title pull-left"><i class="fa fa-ban"></i>&nbsp;&nbsp;Η επιλογή σας δεν έχει αποθηκευτεί</h3>
            <button type="button" class="close pull-right" aria-label="Close" (click)="hideModal('#dangermodal')">
              <span aria-hidden="true"><i class="fa fa-times"></i></span>
            </button>
          </div>
          <div class="modal-body">
            <p>Παρακαλώ προσπαθήστε ξανα!</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Κλείσιμο</button>
          </div>
        </div>
      </div>
    </div>

    <div id="errorselection" (onHidden)="onHidden('#errorselection')" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header modal-header-danger">
            <h3 class="modal-title pull-left"><i class="fa fa-ban"></i>&nbsp;&nbsp;Προέκυψε σφάλμα</h3>
            <button type="button" class="close pull-right" aria-label="Close" (click)="hideModal('#errorselection')">
              <span aria-hidden="true"><i class="fa fa-times"></i></span>
            </button>
          </div>
          <div class="modal-body">
            <p>Προέκυψε σφάλμα κατά τη διαδικασία άντλησης των στοιχείων δήλωσης προτίμησης στο συγκεκριμένο τμήμα του σχολείου σας.</p>
            <p>Παρακαλώ προσπαθείστε ξανά αργότερα.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Κλείσιμο</button>
          </div>
        </div>
      </div>
    </div>

    <div id="emptyselection" (onHidden)="onHidden('#emptyselection')" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header modal-header-danger">
            <h3 class="modal-title pull-left"><i class="fa fa-ban"></i>&nbsp;&nbsp;Δεν υπάρχουν μαθητές</h3>
            <button type="button" class="close pull-right" aria-label="Close" (click)="hideModal('#emptyselection')">
              <span aria-hidden="true"><i class="fa fa-times"></i></span>
            </button>
          </div>
          <div class="modal-body">
            <p>Δεν υπάρχουν μαθητές με δήλωση προτίμησης το συγκεκριμένο τμήμα του σχολείου σας!</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Κλείσιμο</button>
          </div>
        </div>
      </div>
    </div>
    `
})

@Injectable() export default class DirectorViewGym implements OnInit, OnDestroy {

    private CoursesPerSchool$: BehaviorSubject<any>;
    private CoursesPerSchoolSub: Subscription;
    private StudentInfo$: BehaviorSubject<any>;
    private StudentInfoSub: Subscription;
    private retrievedStudent: BehaviorSubject<boolean>;
    private SavedStudentsSub: Subscription;
    private SavedStudents$: BehaviorSubject<any>;
    private courseActive = <number>-1;
    private StudentActive = <number>-1;
    private showLoader: BehaviorSubject<boolean>;
    private opened;
    private applicationId = <number>0;
    private taxi = <number>0;
    private sector = <number>0;
    private special = <number>0;

    constructor(
        private _hds: HelperDataService,
        private activatedRoute: ActivatedRoute,
        private router: Router
    ) {
        this.CoursesPerSchool$ = new BehaviorSubject([{}]);
        this.showLoader = new BehaviorSubject(false);
        this.StudentInfo$ = new BehaviorSubject([{}]);
        this.retrievedStudent = new BehaviorSubject(false);
        this.SavedStudents$ = new BehaviorSubject({});
        this.opened = false;
    }

    public showConfirmModal(): void {
        (<any>$("#applicationDeleteConfirm")).modal("show");
    }

    public showErrorModal(): void {
        (<any>$("#applicationDeleteError")).modal("show");
    }

    public hideConfirmModal(): void {
        (<any>$("#applicationDeleteConfirm")).modal("hide");
    }
    public hideErrorModal(): void {
        (<any>$("#applicationDeleteError")).modal("hide");
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
        (<any>$("#applicationDeleteConfirm")).remove();
        (<any>$("#applicationDeleteError")).remove();
    }

    ngOnInit() {
        (<any>$("#checksaved")).appendTo("body");
        (<any>$("#dangermodal")).appendTo("body");
        (<any>$("#emptyselection")).appendTo("body");
        (<any>$("#errorselection")).appendTo("body");
        (<any>$("#applicationDeleteConfirm")).appendTo("body");
        (<any>$("#applicationDeleteError")).appendTo("body");
        this.showLoader.next(true);
        this.StudentInfoSub = this._hds.FindStudentsPerGym().subscribe(data => {
            this.StudentInfo$.next(data);
            //this.retrievedStudent.next(true);
            this.showLoader.next(false);
        },
        error => {
            this.CoursesPerSchool$.next([{}]);
            this.showLoader.next(false);
            if (error.status === 404) {
                this.showModal("#emptyselection");
            } else {
                this.showModal("#errorselection");
            }
        });
    }
/* 
    findstudent(taxi) {
        this.showLoader.next(true);
        this.retrievedStudent.next(false);
        this.StudentInfoSub = this._hds.getStudentPerSchoolGel(taxi)
            .subscribe(data => {
                this.StudentInfo$.next(data);
                this.retrievedStudent.next(true);
                this.showLoader.next(false);
            },
            error => {
                this.StudentInfo$.next([{}]);
                console.log("Error Getting Students");
                this.showLoader.next(false);
                if (error.status === 404) {
                    this.showModal("#emptyselection");
                } else {
                    this.showModal("#errorselection");
                }
            });

    } */

    setActive(ind) {
        this.StudentActive = -1;
        if (this.courseActive === ind) {
            ind = -1;
        }
        this.courseActive = ind;
    }

    setActiveStudent(ind) {
        this.opened = true;
        if (this.StudentActive === ind) {
            ind = -1;
        }
        this.StudentActive = ind;
    }

    setActiveStudentnew(ind) {
        this.opened = false;
        if (this.StudentActive === ind) {
            ind = -1;
        }
        this.StudentActive = ind;
    }

    confirmStudent(student, cb, ind) {
        let rtype;
        if (cb.value === 1)
            rtype = "1";
        if (cb.value === 2)
            rtype = "0";
        if (cb.value === 3)
            rtype = null;
        let type = cb.value;
        this.showLoader.next(true);

        let std = this.StudentInfo$.getValue();
        std[ind].checkstatus = rtype;

        this.SavedStudentsSub = this._hds.saveConfirmStudents(student, type).subscribe(data => {
            this.SavedStudents$.next(data);
            this.StudentInfo$.next(std);
            this.showLoader.next(false);
            this.showModal("#checksaved");
        },
            error => {
                this.SavedStudents$.next([{}]);
                console.log("Error saving Students");
                this.showLoader.next(false);
                this.showModal("#dangermodal");
            });
    }




}
