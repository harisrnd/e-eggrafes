import { NgRedux } from "@angular-redux/store";
import { Location } from "@angular/common";
import { Component, ElementRef, OnDestroy, OnInit, ViewChild } from "@angular/core";
import { Injectable } from "@angular/core";
import { ActivatedRoute, Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";
import { DataModeActions } from "../../actions/datamode.actions";
import { DATAMODE_INITIAL_STATE } from "../../store/datamode/datamode.initial-state";
import { IDataModeRecords } from "../../store/datamode/datamode.types";

import { EpalClassesActions } from "../../actions/epalclass.actions";
import { IEpalClassRecords } from "../../store/epalclasses/epalclasses.types";
import { StudentDataFieldsActions } from "../../actions/studentdatafields.actions";
import { RegionSchoolsActions } from "../../actions/regionschools.actions";
import { IRegionRecords } from "../../store/regionschools/regionschools.types";
import { SectorCoursesActions } from "../../actions/sectorcourses.actions";
import { ISectorRecords } from "../../store/sectorcourses/sectorcourses.types";
import { SectorFieldsActions } from "../../actions/sectorfields.actions";
import { ISectorFieldRecords } from "../../store/sectorfields/sectorfields.types";



import { SchoolTypeActions } from "../../actions/schooltype.actions";
import { ISchoolTypeRecords } from "../../store/schooltype/schooltype.types";
import { GelClassesActions } from "../../actions/gelclasses.actions";
import { IGelClassRecords } from "../../store/gelclasses/gelclasses.types";
import { ElectiveCourseFieldsActions } from "../../actions/electivecoursesfields.actions";
import { IElectiveCourseFieldRecords } from "../../store/electivecoursesfields/electivecoursesfields.types";
import { OrientationGroupActions } from "../../actions/orientationgroup.action";
import { IOrientationGroupRecords } from "../../store/orientationgroup/orientationgroup.types";
import { GelStudentDataFieldsActions } from "../../actions/gelstudentdatafields.actions";
import { IGelStudentDataFieldRecords } from "../../store/gelstudentdatafields/gelstudentdatafields.types";


import { HelperDataService } from "../../services/helper-data-service";
import { IAppState } from "../../store/store";

@Component({
    selector: "submited-preview",
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
              <p>Επιλέξατε να διαγράψετε τη δήλωση προτίμησης ΕΠΑΛ. Παρακαλούμε επιλέξτε Επιβεβαίωση</p>
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
              <h3 class="modal-title pull-left"><i class="fa fa-ban"></i>&nbsp;&nbsp;Αποτυχία Διαγραφής Δήλωσης Προτίμησης ΕΠΑΛ</h3>
            <button type="button" class="close pull-right" aria-label="Close" (click)="hideModal()">
              <span aria-hidden="true"><i class="fa fa-times"></i></span>
            </button>
          </div>
          <div class="modal-body">
              <p>Η δήλωσή σας δεν διαγράφηκε. Δεν μπορείτε να διαγράψετε τη δήλωσή σας αυτή την περίοδο</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-right" data-dismiss="modal" (click)="hideErrorModal()">Κλείσιμο</button>
          </div>
        </div>
      </div>
    </div>
<div style="min-height: 500px; margin-bottom: 20px;">
    <div class = "loading" *ngIf="(showLoader$ | async) === true"></div>
         <div class="row">
             <breadcrumbs></breadcrumbs>
        </div>

            <div *ngIf="(SubmitedApplic$ | async).length > 0" class="row" style="margin: 10px 2px 10px 2px;">
                <p>Έχουν υποβληθεί οι παρακάτω δηλώσεις προτίμησης ΕΠΑΛ για το νέο σχολικό έτος.</p>
                <p>Επιλέξτε το όνομα ή το επώνυμο του μαθητή για να δείτε αναλυτικά τη δήλωσή σας και να την εκτυπώσετε σε μορφή PDF.</p>
                <p>Μπορείτε να διαγράψετε μία δήλωση επιλέγοντας το εικονίδιο δεξιά από το ονοματεπώνυμο.</p>
                <p>Επιλέξτε "Αρχική" επάνω αριστερά ή κάτω αν θέλετε να ξεκινήσετε την υποβολή νέας δήλωσης προτίμησης.</p>
            </div>
            <div *ngIf="(SubmitedApplic$ | async).length === 0" class="row" style="margin: 10px 2px 10px 2px;">
                <p>Δεν έχετε ακόμη υποβάλλει δήλωση προτίμησης ΕΠΑΛ για το νέο σχολικό έτος.</p>
                <p>Επιλέξτε "Αρχική" επάνω αριστερά ή κάτω αν θέλετε να ξεκινήσετε την υποβολή νέας δήλωσης προτίμησης.</p>
            </div>


              <div *ngIf="(SubmitedApplic$ | async).length > 0" class="row list-group-item" style="margin: 0px 2px 0px 2px; background-color: #ccc;">
                  <div class="col-md-6" style="font-size: 1em; font-weight: bold;">Επώνυμο</div>
                  <div class="col-md-4" style="font-size: 1em; font-weight: bold;">Όνομα</div>
                  <div class="col-md-1" style="font-size: 1em; font-weight: bold;">&nbsp;</div>
                  <div class="col-md-1" style="font-size: 1em; font-weight: bold;">&nbsp;</div>
              </div>

              <div *ngIf="(SubmitedApplic$ | async).length > 0">
               <div class="row list-group-item isclickable"  style="margin: 0px 2px 0px 2px;"
               [class.oddout]="isOdd"
               [class.evenout]="isEven"
               [class.selectedappout]="applicationIdActive === UserData$.id"
               *ngFor="let UserData$  of SubmitedApplic$ | async; let i=index; let isOdd=odd; let isEven=even"  >
                    <div class="col-md-6" style="font-size: 0.8em; font-weight: bold;" (click)="setActiveUser(UserData$.id)">{{UserData$.studentsurname}}</div>
                    <div class="col-md-4" style="font-size: 0.8em; font-weight: bold;" (click)="setActiveUser(UserData$.id)">{{UserData$.name}}</div>
                    <div *ngIf="UserData$.candelete === 1" class="col-md-1" style="font-size: 1em; font-weight: bold;"><i class="fa fa-trash isclickable" (click)="deleteApplication(UserData$.id)"></i></div>
                    <div *ngIf="UserData$.candelete === 0" class="col-md-1" style="font-size: 1em; font-weight: bold;">&nbsp;</div>
                    <!--
                    <div  class="col-md-1" style="font-size: 1em; font-weight: bold;"><i class="fa fa-edit isclickable" (click)="editApplication(UserData$.id)" ></i></div>
                    -->

                    <div style="width: 100%">
                  <div *ngFor="let StudentDetails$  of SubmitedDetails$ | async" [hidden]="UserData$.id !== applicationIdActive" style="margin: 10px 10px 10px 10px;">

                    <div *ngIf = "StudentDetails$.applicantsResultsDisabled == '0'  && !(showLoader$ | async)" >
                      <div *ngIf = "StudentDetails$.status == '1'" >
                        <div class="col-md-12" style="font-size: 1.0em; color: #143147; font-weight: bold;">
                          Η αίτησή σας ικανοποιήθηκε. Έχετε επιλεγεί για να εγγραφείτε στο {{StudentDetails$.schoolName}}.
                          Παρακαλώ να προσέλθετε ΑΜΕΣΑ στο σχολείο για να προχωρήσει η διαδικασία εγγραφής σας σε αυτό, προσκομίζοντας τα απαραίτητα δικαιολογητικά. Διεύθυνση σχολείου: {{StudentDetails$.schoolAddress}}, Τηλέφωνο σχολείου: {{StudentDetails$.schoolTel}}<br><br>
                        </div>
                      </div>
                      <div *ngIf = "StudentDetails$.status == '2' " >
                        <div class="col-md-12" style="font-size: 1.0em; color: #a52a2a; font-weight: bold;">
                          Η αίτησή σας είναι σε εκκρεμότητα. Για την τοποθέτησή σας και τις ενέργειες που πρέπει να
                          κάνετε θα ενημερωθείτε με νεότερο μήνυμα.<br><br>
                        </div>
                      </div>
                      <div *ngIf = "StudentDetails$.status == '3' " >
                        <div class="col-md-12" style="font-size: 1.0em; color: #a52a2a; font-weight: bold;">
                          Η αίτησή σας δεν ικανοποιήθηκε. Μπορείτε να κάνετε νέα αίτηση στην επόμενη περίοδο δηλώσεων προτίμησης.<br><br>
                        </div>
                      </div>
                    </div>


                  <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                      <div class="col-md-3" style="font-size: 0.8em;">Αριθμός Δήλωσης Προτίμησης ΕΠΑΛ</div>
                      <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{StudentDetails$.applicationId}}</div>
                      <div class="col-md-3" style="font-size: 0.8em;">Υποβλήθηκε</div>
                      <div c
                      lass="col-md-3" style="font-size: 0.8em; font-weight: bold">{{StudentDetails$.changed}}</div>
                  </div>
                  <div class="row evenin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                      <div class="col-md-12" style="font-size: 1em; font-weight: bold; text-align: center;">Στοιχεία αιτούμενου</div>
                  </div>

                  <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                      <div class="col-md-3" style="font-size: 0.8em;">Όνομα</div>
                      <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{StudentDetails$.guardian_name}}</div>
                      <div class="col-md-3" style="font-size: 0.8em;">Επώνυμο</div>
                      <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{StudentDetails$.guardian_surname}}</div>
                  </div>
                  <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                      <div class="col-md-3" style="font-size: 0.8em;">Όνομα πατέρα</div>
                      <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{ StudentDetails$.guardian_fathername }}</div>
                      <div class="col-md-3" style="font-size: 0.8em;">Όνομα μητέρας</div>
                      <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{ StudentDetails$.guardian_mothername }}</div>
                  </div>
                  <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                      <div class="col-md-3" style="font-size: 0.8em;">Διεύθυνση</div>
                      <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{StudentDetails$.regionaddress}}</div>
                      <div class="col-md-3" style="font-size: 0.8em;">ΤΚ - Πόλη</div>
                      <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{StudentDetails$.regiontk}} - {{StudentDetails$.regionarea}}</div>
                  </div>

                  <div class="row evenin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                      <div class="col-md-12" style="font-size: 1em; font-weight: bold; text-align: center;">Στοιχεία μαθητή</div>
                  </div>
                  <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                      <div class="col-md-3" style="font-size: 0.8em;">Όνομα μαθητή</div>
                      <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{StudentDetails$.name}}</div>
                      <div class="col-md-3" style="font-size: 0.8em;">Επώνυμο μαθητή</div>
                      <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{StudentDetails$.studentsurname}}</div>
                  </div>
                  <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                      <div class="col-md-3" style="font-size: 0.8em;">Όνομα Πατέρα</div>
                      <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{StudentDetails$.fatherfirstname}}</div>
                      <div class="col-md-3" style="font-size: 0.8em;">Όνομα Μητέρας</div>
                      <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{StudentDetails$.motherfirstname}}</div>
                  </div>
                  <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                      <div class="col-md-3" style="font-size: 0.8em;">Ημερομηνία Γέννησης</div>
                      <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{StudentDetails$.birthdate}}</div>
                      <div class="col-md-3" style="font-size: 0.8em;">Τηλέφωνο Επικοινωνίας</div>
                      <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{StudentDetails$.telnum}}</div>
                  </div>

                  <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                      <div class="col-md-3" style="font-size: 0.8em;">Σχολείο τελευταίας φοίτησης</div>
                      <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{StudentDetails$.lastschool_schoolname}}</div>
                      <div class="col-md-3" style="font-size: 0.8em;">Σχολικό έτος τελευταίας φοίτησης</div>
                      <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{StudentDetails$.lastschool_schoolyear}}</div>
                  </div>

                  <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                      <div class="col-md-3" style="font-size: 0.8em;">Τάξη τελευταίας φοίτησης</div>
                      <div *ngIf="StudentDetails$.lastschool_class === '1'" class="col-md-9" style="font-size: 0.8em; font-weight: bold">Α</div>
                      <div *ngIf="StudentDetails$.lastschool_class === '2'" class="col-md-9" style="font-size: 0.8em; font-weight: bold">Β</div>
                      <div *ngIf="StudentDetails$.lastschool_class === '3'" class="col-md-9" style="font-size: 0.8em; font-weight: bold">Γ</div>
                      <div *ngIf="StudentDetails$.lastschool_class === '4'" class="col-md-9" style="font-size: 0.8em; font-weight: bold">Δ</div>
                  </div>
                  <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                      <div class="col-md-3" style="font-size: 0.8em;">Δήλωση από:</div>
                      <div class="col-md-9" style="font-size: 0.8em; font-weight: bold">{{ StudentDetails$.relationtostudent }}</div>
                  </div>
                  <div class="row evenin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                      <div class="col-md-12" style="font-size: 1em; font-weight: bold; text-align: center">Επιλεχθέντα Σχολεία</div>
                  </div>
                  <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                      <div class="col-md-3" style="font-size: 0.8em;">Τάξη φοίτησης για το νέο σχολικό έτος</div>
                      <div *ngIf="StudentDetails$.currentclass === '1'" class="col-md-9" style="font-size: 0.8em; font-weight: bold">Α</div>
                      <div *ngIf="StudentDetails$.currentclass === '2'" class="col-md-9" style="font-size: 0.8em; font-weight: bold">Β</div>
                      <div *ngIf="StudentDetails$.currentclass === '3'" class="col-md-9" style="font-size: 0.8em; font-weight: bold">Γ</div>
                      <div *ngIf="StudentDetails$.currentclass === '4'" class="col-md-9" style="font-size: 0.8em; font-weight: bold">Δ</div>
                  </div>

                  <div *ngIf="StudentDetails$.currentclass === '2'" class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                      <div class="col-md-3" style="font-size: 0.8em;">Τομέας φοίτησης για το νέο σχολικό έτος</div>
                      <div class="col-md-9" style="font-size: 0.8em; font-weight: bold">{{StudentDetails$.currentsector}}</div>
                  </div>
                  <div *ngIf="StudentDetails$.currentclass === '3' || StudentDetails$.currentclass === '4'" class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                      <div class="col-md-3" style="font-size: 0.8em;">Ειδικότητα φοίτησης για το νέο σχολικό έτος</div>
                      <div class="col-md-9" style="font-size: 0.8em; font-weight: bold">{{StudentDetails$.currentcourse}}</div>
                  </div>

                    <div class="row evenin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div class="col-md-6" style="font-size: 1em; font-weight: bold; text-align: center;">Σειρά Προτίμησης</div>
                        <div class="col-md-6" style="font-size: 1em; font-weight: bold;">Επιλογή ΕΠΑΛ</div>
                    </div>

                    <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;" *ngFor="let epalChoices$  of StudentDetails$['epalSchoolsChosen']; let i=index; let isOdd=odd; let isEven=even" [hidden]="UserData$.id !== applicationIdActive">
                        <div class="col-md-6" style="font-size: 0.8em; font-weight: bold; text-align: center;">{{epalChoices$.choice_no}}</div>
                        <div class="col-md-6" style="font-size: 0.8em; font-weight: bold;">{{epalChoices$.epal_id}}</div>
                    </div>

                    <div class="row" style="margin-top: 20px; margin-bottom: 20px;">
                        <div class="col-md-12">
                          <!--
                          ενεργοποίηση Τροποποίησης Αίτησης όταν: δεν υπάρχει αποτέλεσμα κατανομής για αυτήν την αίτηση
                          ΚΑΙ επιτρέπεται η τροποποίηση αιτήσεων
                          -->
                          <div *ngIf = "StudentDetails$.status == '3' || StudentDetails$.status == '4'" >
                            <button type="button" class="btn-primary btn-lg pull-left isclickable" style="width: 10em;" (click)="editApplication()">
                                <span style="font-size: 0.9em; font-weight: bold;">Επεξεργασία&nbsp;&nbsp;&nbsp;</span>
                            </button>
                          </div>
                            <button type="button" class="btn-primary btn-lg pull-right isclickable" style="width: 10em;" (click)="createPdfServerSide()">
                                <span style="font-size: 0.9em; font-weight: bold;">Εκτύπωση(PDF)&nbsp;&nbsp;&nbsp;</span>
                            </button>
                        </div>
                    </div>

                    </div>
                </div>

              </div>
              </div>

              <div class="row" style="margin-top: 20px; margin-bottom: 20px;">
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


              <button type="button" class="btn-primary btn-lg pull-left isclickable" style="width: 9em;" (click)="editEpalApplication()" >
                  <span style="font-size: 0.9em; font-weight: bold;">Τεστ</span>
              </button>

              </div>

   `
})

@Injectable() export default class SubmitedPreview implements OnInit, OnDestroy {

    private SubmitedApplic$: BehaviorSubject<any>;
    private SubmitedUsersSub: Subscription;
    private SubmitedDetails$: BehaviorSubject<any>;
    private SubmitedDetailsSub: Subscription;

    private electivecourseFieldsSub: Subscription;
    private OrientationGroupSub: Subscription;
    private sectorFieldsSub: Subscription;
    private regionsSub: Subscription;
    private sectorsSub: Subscription;

    private showLoader$: BehaviorSubject<boolean>;
    private isModalShown: BehaviorSubject<boolean>;
    private applicationIdActive = <number>-1;

    private applicationId = <number>0;

    //demo data
    private schooltype_id = <number>1;
    private schooltype_name = <string>"ΓΕΛ";
    private class_id = <number>2;
    private electiveCourse_id = <string>"4";
    private electiveCourse_index = <number>-1;
    private orientationGroup_id = <string>"16";
    private orientationGroup_index = <number>-1;

    private class_epal_id = <string>"1";
    private sector_id = <string>"8";
    private sector_index = <number>-1;
    private school_id = <string>"25"; //152
    private school_index = <number>-1;
    private region_index = <number>-1;
    private course_id = <string>"17";
    private course_index = <number>-1;
    private sectorcourse_index = <number>-1;

    //private class_name_epal = <string>"Γ' Λυκείου";

    //end demo data

    @ViewChild("target") element: ElementRef;

    constructor(private _ngRedux: NgRedux<IAppState>,
        private _cfa: DataModeActions,

        private _eca: EpalClassesActions,
        private _sdfa: StudentDataFieldsActions,
        private _csa: SectorCoursesActions,
        private _sfa: SectorFieldsActions,
        private _rsa: RegionSchoolsActions,

        private _sta: SchoolTypeActions,
        private _gca: GelClassesActions,
        private _ecf: ElectiveCourseFieldsActions,
        private _ogs: OrientationGroupActions,
        private _gsdf: GelStudentDataFieldsActions,

        private _hds: HelperDataService,
        private activatedRoute: ActivatedRoute,
        private router: Router,
        private loc: Location
    ) {
        // this.datamode$ = new BehaviorSubject(DATAMODE_INITIAL_STATE);
        this.SubmitedApplic$ = new BehaviorSubject([{}]);
        this.SubmitedDetails$ = new BehaviorSubject([{}]);
        this.showLoader$ = new BehaviorSubject(false);
        this.isModalShown = new BehaviorSubject(false);
    }

    ngOnDestroy() {
        (<any>jQuery("#applicationDeleteConfirm")).remove();
        (<any>jQuery("#applicationDeleteError")).remove();
        if (this.SubmitedUsersSub)
            this.SubmitedUsersSub.unsubscribe();
        if (this.SubmitedDetailsSub)
            this.SubmitedDetailsSub.unsubscribe();
        if (this.electivecourseFieldsSub)
            this.electivecourseFieldsSub.unsubscribe();
        if (this.OrientationGroupSub)
            this.OrientationGroupSub.unsubscribe();
        if (this.sectorFieldsSub)
            this.sectorFieldsSub.unsubscribe();
        if (this.regionsSub)
            this.regionsSub.unsubscribe();
        if (this.sectorsSub)
            this.sectorsSub.unsubscribe();
        // if (this.datamodeSub)
        // this.datamodeSub.unsubscribe();
    }

    ngOnInit() {

        this.createStoreWithEpalAppData();



        (<any>jQuery("#applicationDeleteConfirm")).appendTo("body");
        (<any>jQuery("#applicationDeleteError")).appendTo("body");
        this.showLoader$.next(true);

        this.SubmitedUsersSub = this._hds.getSubmittedPreviw().subscribe(
            data => {
                this.SubmitedApplic$.next(data);
                this.showLoader$.next(false);
            },
            error => {
                this.SubmitedApplic$.next([{}]);
                this.showLoader$.next(false);
                console.log("Error Getting Schools");
            });
    }


    setActiveUser(ind: number): void {
        if (ind === this.applicationIdActive) {
            this.applicationIdActive = 0;
            return;
        }
        this.applicationIdActive = ind;
        this.showLoader$.next(true);

        this.SubmitedDetailsSub = this._hds.getStudentDetails(this.applicationIdActive).subscribe(data => {
            this.SubmitedDetails$.next(data);
            this.showLoader$.next(false);
        },
            error => {
                this.SubmitedDetails$.next([{}]);
                console.log("Error Getting Schools");
                this.showLoader$.next(false);
            });

    }

    createPdfServerSide() {
        this._hds.createPdfServerSide(this.applicationIdActive, this.SubmitedDetails$.getValue()[0].status);
    }


    deleteApplication(appId: number): void {
        this.applicationId = appId;
        this.showConfirmModal();
    }

    editApplication(appId: number): void {

        this.applicationId = appId;
        this.router.navigate(["/epal-class-select"]);
        // this.router.navigate(["/intro-statement"]);

        this._sfa.initSectorFields();
        this._rsa.initRegionSchools();
        this._csa.initSectorCourses();

        // this._cfa.saveEpalClassesSelected({name: this.SubmitedDetails$.getValue()[0].currentclass, appmode: "edit", studentfirsttname: this.SubmitedDetails$.getValue()[0].name});
        this._cfa.saveDataModeSelected({edit: true, edit_class: true, app_update: true, currentclass: this.SubmitedDetails$.getValue()[0].currentclass,
          appid: this.SubmitedDetails$.getValue()[0].applicationId,  studentfirstname: this.SubmitedDetails$.getValue()[0].name,
          studentsurname: this.SubmitedDetails$.getValue()[0].studentsurname, fatherfirstname: this.SubmitedDetails$.getValue()[0].fatherfirstname,
          motherfirstname: this.SubmitedDetails$.getValue()[0].motherfirstname, studentbirthdate: this.SubmitedDetails$.getValue()[0].birthdate,
          regionaddress: this.SubmitedDetails$.getValue()[0].regionaddress, regiontk: this.SubmitedDetails$.getValue()[0].regiontk,
          regionarea: this.SubmitedDetails$.getValue()[0].regionarea, lastschool_schoolname: this.SubmitedDetails$.getValue()[0].lastschool_schoolname,
          lastschool_registrynumber: this.SubmitedDetails$.getValue()[0].lastschool_registrynumber, lastschool_unittypeid: this.SubmitedDetails$.getValue()[0].lastschool_unittypeid,
          lastschool_schoolyear: this.SubmitedDetails$.getValue()[0].lastschool_schoolyear, lastschool_class: this.SubmitedDetails$.getValue()[0].lastschool_class,
          relationtostudent: this.SubmitedDetails$.getValue()[0].relationtostudent, telnum: this.SubmitedDetails$.getValue()[0].telnum,
          sector_name: this.SubmitedDetails$.getValue()[0].currentsector, course_name: this.SubmitedDetails$.getValue()[0].currentcourse,
          epal_name_choice: this.SubmitedDetails$.getValue()[0].epalSchoolsChosen
        });

    }

    deleteApplicationDo(): void {
        this.hideConfirmModal();
        this.showLoader$.next(true);
        this._hds.deleteApplication(this.applicationId).then(data => {
            this.SubmitedUsersSub.unsubscribe();

            this.SubmitedUsersSub = this._hds.getSubmittedPreviw().subscribe(
                data => {
                    this.SubmitedApplic$.next(data);
                    this.showLoader$.next(false);
                },
                error => {
                    this.SubmitedApplic$.next([{}]);
                    this.showLoader$.next(false);
                    console.log("Error Getting Schools");
                });

        }).catch(err => {
            this.showLoader$.next(false);
            this.showErrorModal();
            console.log(err);
        });
    }

    public showConfirmModal(): void {
        (<any>jQuery("#applicationDeleteConfirm")).modal("show");
    }

    public showErrorModal(): void {
        (<any>jQuery("#applicationDeleteError")).modal("show");
    }

    public hideConfirmModal(): void {
        (<any>jQuery("#applicationDeleteConfirm")).modal("hide");
    }
    public hideErrorModal(): void {
        (<any>jQuery("#applicationDeleteError")).modal("hide");
    }

    public onHidden(): void {
        this.isModalShown.next(false);
    }

    public goBack(): void {
        this.loc.back();

    }

    public goHome(): void {
        this.router.navigate([""]);
    }

    editGelApplication()  {

      this._sta.saveSchoolTypeSelected(this.schooltype_id, this.schooltype_name);

      this._gca.saveGelClassesSelected(-1, this.class_id-1);

      if (this.electiveCourse_index != -1)
        this._ecf.saveElectiveCourseFieldsSelected(this.electiveCourse_index, 0, 0);
        //this._ecf.saveElectiveCourseFieldsSelected(4, 0, 0);

      if (this.orientationGroup_index != -1)
        this._ogs.saveOrientationGroupSelected(this.orientationGroup_index, 0);

      this.router.navigate(["/gel-class-select"]);

    }

    editEpalApplication() {

      this._sta.saveSchoolTypeSelected(this.schooltype_id, this.schooltype_name);

      this._eca.saveEpalClassesSelected({name: this.class_epal_id});

      if (this.sector_index != -1)
        this._sfa.saveSectorFieldsSelected(-1, this.sector_index);

      if (this.school_index != -1 && this.region_index != -1)
        this._rsa.saveRegionSchoolsSelected(true, this.region_index, this.school_index);

      if (this.course_index != -1 && this.sectorcourse_index != -1)
        this._csa.saveSectorCoursesSelected(-1, -1, true, this.sectorcourse_index, this.course_index);

      this.router.navigate(["/epal-class-select"]);

    }

    createStoreWithEpalAppData()  {

        this._eca.initEpalClasses();

        if (this.class_epal_id === "2" ) {
          console.log("Mpika");
          this._sfa.getSectorFields(false);
        }

        if (this.class_epal_id === "3" || this.class_epal_id === "4" ) {
          console.log("Mpika edo");
          this._csa.getSectorCourses(false);
        }

        if (this.class_epal_id === "1" )
          this._rsa.getRegionSchools(parseInt(this.class_epal_id), "-1", false);
        else if (this.class_epal_id === "2" )
          this._rsa.getRegionSchools(parseInt(this.class_epal_id), parseInt(this.sector_id), false);
        else if (this.class_epal_id === "3" || this.class_epal_id === "4" )
          this._rsa.getRegionSchools(parseInt(this.class_epal_id), parseInt(this.course_id), false);



        this.sectorFieldsSub = this._ngRedux.select("sectorFields")
              .map(sectorFields => <ISectorFieldRecords>sectorFields)
              .subscribe(sfds => {
                  let seccnt = 0;
                  sfds.reduce(({}, sectorField) => {
                      ++seccnt;
                      if (sectorField.get("id") === this.sector_id ) {
                        this.sector_index = seccnt -1;
                        console.log("sector index:");
                        console.log(seccnt);
                      }

                      return sectorField;
                  }, {});
              }, error => { console.log("error selecting sectorFields"); });


          this.regionsSub = this._ngRedux.select("regions")
                  .subscribe(regions => {
                      let rgns = <IRegionRecords>regions;
                      let numsel = 0;
                      let numreg = 0;
                      rgns.reduce((prevRegion, region) => {
                          numreg++;
                          numsel = 0;
                          region.get("epals").reduce((prevEpal, epal) => {
                              ++numsel;
                              if (epal.get("epal_id") === this.school_id ) {
                                this.school_index = numsel -1;
                                this.region_index = numreg - 1;
                                console.log("school index:");
                                console.log(numsel);
                                console.log("region index:");
                                console.log(numreg);
                              }
                              return epal;
                          }, {});
                          return region;
                      }, {});
                }, error => { console.log("error selecting regions"); });


              //let ids = 0, idc = 0;
              this.sectorsSub = this._ngRedux.select("sectors")
                    //.map(sectors => <ISectorRecords>sectors)
                    .subscribe(sectors => {
                        let secs = <ISectorRecords>sectors;
                        let numcour = 0;
                        let numsec = 0;
                        secs.reduce((prevSector, sector) => {
                            ++numsec;
                            numcour = 0;
                            sector.get("courses").reduce((prevCourse, course) => {
                                ++numcour;
                                if (course.get("course_id") === this.course_id ) {
                                  this.course_index = numcour -1;
                                  this.sectorcourse_index = numsec - 1;
                                  console.log("course index:");
                                  console.log(numcour);
                                  console.log("sector index:");
                                  console.log(numsec);
                                }
                                return course;
                            }, {});
                            return sector;
                        }, {});
                    }, error => { console.log("error selecting sectors"); });

    }

    createStoreWithGelAppData()  {

      this._gca.resetGelClassesSelected();

      if (this.class_id === 1 || this.class_id === 3 || this.class_id === 4)
        this._ecf.getElectiveCourseFields(false, this.class_id);

      if (this.class_id === 2 || this.class_id === 3 || this.class_id === 6 || this.class_id === 7)
        this._ogs.getOrientationGroups(false, this.class_id, 'ΟΠ');

      this._gsdf.saveGelStudentDataFields([{name: "ΑΛΙΚΗ", studentsurname: "ΚΑΤΣΑΟΥΝΟΥ",
          fatherfirstname: "ΑΝΑΣΤΑΣΙΟΣ", motherfirstname: "ΦΡΕΙΔΕΡΙΚΗ",
          regionaddress: "ΧΑΤΖΗ Β. 16", regiontk: "26334", regionarea: "ΠΑΤΡΑ",
          lastschool_schoolname: {registry_no: "0656010", name: "ΛΑΠΠΑ", unit_type_id: 4},
          lastschool_schoolyear: "1998-1999", lastschool_class: "2",
          relationtostudent: "Γονέας/Κηδεμόνας", telnum: "2610331498",
          studentbirthdate: {date: {year: 1995, month: 2, day: 25}}
        }]);



      this.electivecourseFieldsSub = this._ngRedux.select("electivecourseFields")
          .map(electivecourseFields => <IElectiveCourseFieldRecords>electivecourseFields)
          .subscribe(sfds => {
              let electcnt = 0;
              sfds.reduce(({}, electivecourseField) => {
                  ++electcnt;
                  //console.log("size of electivecourses list:");
                  //console.log(cnt);
                  console.log(electivecourseField.get("id"));
                  if (electivecourseField.get("id")=== this.electiveCourse_id ) {
                    this.electiveCourse_index = electcnt -1;
                    console.log("elective index:");
                    console.log(electcnt);
                  }
                  return electivecourseField;
              }, {});
              //this.electivecourseFields$.next(sfds);
          }, error => { console.log("error selecting electivecourseFields"); });


          this.OrientationGroupSub = this._ngRedux.select("orientationGroup")
              .map(orientationGroup => <IOrientationGroupRecords>orientationGroup)
              .subscribe(ogs => {
                    let orientcnt = 0;
                    ogs.reduce(({}, orientationgroup) => {
                      ++orientcnt;
                      console.log(orientationgroup.get("id"));
                      if (orientationgroup.get("id") === this.orientationGroup_id) {
                          this.orientationGroup_index = orientcnt-1;
                          console.log("orientation index:");
                          console.log(orientcnt);
                      }
                      return orientationgroup;
                    }, {});
                    //this.OrientationGroup$.next(ogs);
              }, error => { console.log("error selecting orientation"); });

    }




}
