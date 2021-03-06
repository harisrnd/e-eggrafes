import { NgRedux } from "@angular-redux/store";
import { Location } from "@angular/common";
import { Component, ElementRef, OnDestroy, OnInit, ViewChild } from "@angular/core";
import { Injectable } from "@angular/core";
import { ActivatedRoute, Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";
import { DataModeActions } from "../../actions/datamode.actions";
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
import { LangCourseFieldsActions } from "../../actions/langcoursesfields.actions";
import { ILangCourseFieldRecords } from "../../store/langcoursesfields/langcoursesfields.types";
import { OrientationGroupActions } from "../../actions/orientationgroup.action";
import { IOrientationGroupRecords } from "../../store/orientationgroup/orientationgroup.types";
import { GelStudentDataFieldsActions } from "../../actions/gelstudentdatafields.actions";
import { IGelStudentDataFieldRecords } from "../../store/gelstudentdatafields/gelstudentdatafields.types";

import { ILoginInfoRecords } from "../../store/logininfo/logininfo.types";

import { HelperDataService } from "../../services/helper-data-service";
import { IAppState } from "../../store/store";

@Component({
    selector: "submited-preview",
    template: `
    <div id="applicationDeleteConfirm" (onHidden)="onHidden()" class="modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header modal-header-danger">
              <h3 class="modal-title pull-left"><i class="fa fa-close"></i>&nbsp;&nbsp;Διαγραφή Δήλωσης Προτίμησης</h3>
            <button type="button" class="close pull-right" aria-label="Close" (click)="hideConfirmModal()">
              <span aria-hidden="true"><i class="fa fa-times"></i></span>
            </button>
          </div>
          <div class="modal-body">
              <p>Επιλέξατε να διαγράψετε τη δήλωση προτίμησης σας. Παρακαλούμε επιλέξτε Επιβεβαίωση</p>
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
              <h3 class="modal-title pull-left"><i class="fa fa-ban"></i>&nbsp;&nbsp;Αποτυχία Διαγραφής Δήλωσης Προτίμησης</h3>
            <button type="button" class="close pull-right" aria-label="Close" (click)="hideErrorModal()">
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


        <div *ngIf="(SubmitedApplic$ | async).length > 0 || (GelSubmittedApplic$ | async).length > 0" class="row" style="margin: 10px 2px 10px 2px;">
            <p>Έχουν υποβληθεί οι παρακάτω δηλώσεις προτίμησης για το νέο σχολικό έτος.</p>
            <p>Επιλέξτε το όνομα ή το επώνυμο του μαθητή για να δείτε αναλυτικά τη δήλωσή σας και να την εκτυπώσετε σε μορφή PDF.</p>
            <p>Μπορείτε να διαγράψετε μία δήλωση επιλέγοντας το εικονίδιο δεξιά από το ονοματεπώνυμο.</p>
            <p>Επιλέξτε "Αρχική" επάνω αριστερά ή κάτω αν θέλετε να ξεκινήσετε την υποβολή νέας δήλωσης προτίμησης.</p>
        </div>
        <div *ngIf="(SubmitedApplic$ | async).length === 0 && (GelSubmittedApplic$ | async).length == 0" class="row" style="margin: 10px 2px 10px 2px;">
            <p>Δεν έχετε ακόμη υποβάλλει δήλωση προτίμησης για το νέο σχολικό έτος.</p>
            <p>Επιλέξτε "Αρχική" επάνω αριστερά ή κάτω αν θέλετε να ξεκινήσετε την υποβολή νέας δήλωσης προτίμησης.</p>
        </div>

        <div *ngIf="(SubmitedApplic$ | async).length > 0 || (GelSubmittedApplic$ | async).length > 0" class="row list-group-item" style="margin: 0px 2px 0px 2px; background-color: #ccc;">
            <div class="col-md-5" style="font-size: 1em; font-weight: bold;">Επώνυμο</div>
            <div class="col-md-4" style="font-size: 1em; font-weight: bold;">Όνομα</div>
            <div class="col-md-2" style="font-size: 1em; font-weight: bold;">Τύπος Σχολείου</div>
            <div class="col-md-1" style="font-size: 1em; font-weight: bold;">&nbsp;</div>
        </div>

<!--
*ngIf="(GelSubmittedApplic$ | async).length > 0"
-->
        <div *ngIf="(GelSubmittedApplic$ | async).length > 0">
            <div class="row list-group-item isclickable"  style="margin: 0px 2px 0px 2px;" [class.oddout]="isOdd" [class.evenout]="isEven" [class.selectedappout]="applicationGelIdActive === UserData$.id"
            *ngFor="let UserData$  of GelSubmittedApplic$ | async; let i=index; let isOdd=odd; let isEven=even" >

                <div class="col-md-5" style="font-size: 0.8em; font-weight: bold;" (click)="setActiveGelUser(UserData$.id)">{{UserData$.studentsurname}}</div>
                <div class="col-md-4" style="font-size: 0.8em; font-weight: bold;" (click)="setActiveGelUser(UserData$.id)">{{UserData$.name}}</div>
                <div class="col-md-2" style="font-size: 0.8em; font-weight: bold;" (click)="setActiveGelUser(UserData$.id)">{{UserData$.logo}}</div>
                <div *ngIf="UserData$.candelete === 1" class="col-md-1 text-right" style="font-size: 1em; font-weight: bold;"><i class="fa fa-trash isclickable" (click)="deleteGelApplication(UserData$.id)"></i></div>
                <div *ngIf="UserData$.candelete === 0" class="col-md-1" style="font-size: 1em; font-weight: bold;">&nbsp;</div>

                <div style="width: 100%">
                <div *ngFor="let GelStudentDetails$  of GelSubmittedDetails$ | async" [hidden]="UserData$.id !== applicationGelIdActive" style="margin: 10px 10px 10px 10px;">

                      <div *ngIf = "GelStudentDetails$.applicantsResultsDisabled == '0'  && !(showLoader$ | async)" >
                          <div *ngIf = "GelStudentDetails$.status == '1'|| GelStudentDetails$.status == '4' " >
                              <div class="col-md-12" style="font-size: 1.0em; color: #21610B; font-weight: bold;">
                                  Η αίτησή σας ικανοποιήθηκε. Έχετε επιλεγεί για να εγγραφείτε στο {{GelStudentDetails$.schoolName}}.
                                  Μπορείτε να προσέλθετε στο σχολείο για να ολοκληρωθεί η διαδικασία εγγραφής σας σε αυτό,
                                  προσκομίζοντας τα απαραίτητα δικαιολογητικά μέχρι την αρχή της σχολικής χρονιάς.
                                  Διεύθυνση σχολείου: {{GelStudentDetails$.schoolAddress}}, Τηλέφωνο σχολείου: {{GelStudentDetails$.schoolTel}}.<br><br>
                              </div>
                          </div>
                          <div *ngIf = "GelStudentDetails$.status == '3' " >
                              <div class="col-md-12" style="font-size: 1.0em; color: #a52a2a; font-weight: bold;">
                                  Η αίτησή σας είναι σε κατάσταση διεκπεραίωσης από την οικεία Διεύθυνση Δευτεροβάθμιας Εκπαίδευσης.<br><br>
                              </div>
                          </div>
                          <div *ngIf = "GelStudentDetails$.status == '5' " >
                              <div class="col-md-12" style="font-size: 1.0em; color: #a52a2a; font-weight: bold;">
                                  Δεν ήταν δυνατή η τοποθέτησή σας σε σχολείο λόγω του αποτελέσματος φοίτησης.
                                  Μπορείτε να προχωρήσετε σε τροποποίηση της αίτησης στη Β’ περίοδο αιτήσεων.<br><br>
                              </div>
                          </div>
                          <div *ngIf = "GelStudentDetails$.status == '6' " >
                              <div class="col-md-12" style="font-size: 1.0em; color: #a52a2a; font-weight: bold;">
                                  Δεν ήταν δυνατή η τοποθέτησή σας σε σχολείο λόγω του τρέχοντος αποτελέσματος προαγωγής.
                                  Σε περίπτωση προαγωγής σας τις αμέσως επόμενες ημέρες θα τοποθετηθείτε σε σχολείο χωρίς καμία δική σας περαιτέρω ενέργεια.<br><br>
                              </div>
                          </div>
                          <div *ngIf = "GelStudentDetails$.status == '7' " >
                              <div class="col-md-12" style="font-size: 1.0em; color: #a52a2a; font-weight: bold;">
                                  Το αποτέλεσμα της προαγωγής σας δεν έχει καταχωρισθεί στo πληροφοριακό σύστημα του σχολείου (myschool).
                                  Παρακαλώ επικοινωνήστε με τον διευθυντή του σχολείου σας.<br><br>
                              </div>
                          </div>
                          <div *ngIf = "GelStudentDetails$.status == '8' " >
                              <div class="col-md-12" style="font-size: 1.0em; color: #a52a2a; font-weight: bold;">
                                  Η δήλωσή σας φαίνεται να έχει λάθος στοιχεία. Μπορείτε να προχωρήσετε σε τροποποίηση ή διαγραφή και νέα αίτηση κατά τη Β’ περίοδο αιτήσεων.
                                  Δώστε ιδιαίτερη προσοχή στα πεδία: Σχολείο τελευταίας φοίτησης (για τους ενεργούς μαθητές είναι το σχολείο που φοιτούν αυτή τη στιγμή),
                                  Τάξη τελευταίας φοίτησης, Τάξη φοίτησης στο ερχόμενο σχολικό έτος.<br><br>
                              </div>
                          </div>
                      </div>


                    <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div class="col-md-3" style="font-size: 0.8em;">Αριθμός Δήλωσης Προτίμησης ΓΕΛ</div>
                        <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{GelStudentDetails$.applicationId}}</div>
                        <div class="col-md-3" style="font-size: 0.8em;">Υποβλήθηκε</div>
                        <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{GelStudentDetails$.changed}}</div>
                    </div>

                    <div class="row evenin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div class="col-md-12" style="font-size: 1em; font-weight: bold; text-align: center;">Στοιχεία Αιτούμενου</div>
                    </div>

                    <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div class="col-md-3" style="font-size: 0.8em;">Όνομα</div>
                        <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{GelStudentDetails$.guardian_name}}</div>
                        <div class="col-md-3" style="font-size: 0.8em;">Επώνυμο</div>
                        <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{GelStudentDetails$.guardian_surname}}</div>
                    </div>

                    <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div class="col-md-3" style="font-size: 0.8em;">Όνομα πατέρα</div>
                        <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{ GelStudentDetails$.guardian_fathername }}</div>
                        <div class="col-md-3" style="font-size: 0.8em;">Όνομα μητέρας</div>
                        <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{ GelStudentDetails$.guardian_mothername }}</div>
                    </div>

                    <div class="row evenin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div class="col-md-12" style="font-size: 1em; font-weight: bold; text-align: center;">Στοιχεία Φοίτησης Μαθητή</div>
                    </div>

                    <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div class="col-md-3" style="font-size: 0.8em;">Σχολείο τελευταίας φοίτησης</div>
                        <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{GelStudentDetails$.lastschool_schoolname}}</div>
                        <div class="col-md-3" style="font-size: 0.8em;">Σχολικό έτος τελευταίας φοίτησης</div>
                        <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{GelStudentDetails$.lastschool_schoolyear}}</div>
                    </div>

                    <div *ngIf="(GelStudentDetails$.lastschool_class !== '' && GelStudentDetails$.lastschool_schoolyear < '2013-2014') || (wsEnabled | async)===0 || GelStudentDetails$.lastschool_unittypeid =='40'" class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div class="col-md-3" style="font-size: 0.8em;">Τάξη τελευταίας φοίτησης</div>
                        <div *ngIf="GelStudentDetails$.lastschool_class === '1'" class="col-md-9" style="font-size: 0.8em; font-weight: bold">Α</div>
                        <div *ngIf="GelStudentDetails$.lastschool_class === '2'" class="col-md-9" style="font-size: 0.8em; font-weight: bold">Β</div>
                        <div *ngIf="GelStudentDetails$.lastschool_class === '3'" class="col-md-9" style="font-size: 0.8em; font-weight: bold">Γ</div>
                        <div *ngIf="GelStudentDetails$.lastschool_class === '4'" class="col-md-9" style="font-size: 0.8em; font-weight: bold">Δ</div>
                    </div>

                    <div class="row evenin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div class="col-md-12" style="font-size: 1em; font-weight: bold; text-align: center;">Προσωπικά Στοιχεία Μαθητή</div>
                    </div>

                    <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div class="col-md-3" style="font-size: 0.8em;">Όνομα μαθητή</div>
                        <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{GelStudentDetails$.name}}</div>
                        <div class="col-md-3" style="font-size: 0.8em;">Επώνυμο μαθητή</div>
                        <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{GelStudentDetails$.studentsurname}}</div>
                    </div>

                    <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div class="col-md-3" style="font-size: 0.8em;">Όνομα Πατέρα</div>
                        <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{GelStudentDetails$.fatherfirstname}}</div>
                        <div class="col-md-3" style="font-size: 0.8em;">Όνομα Μητέρας</div>
                        <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{GelStudentDetails$.motherfirstname}}</div>
                    </div>

                    <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div class="col-md-3" style="font-size: 0.8em;">Ημερομηνία Γέννησης</div>
                        <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{GelStudentDetails$.birthdate}}</div>
                        <div *ngIf="GelStudentDetails$.am!='' && GelStudentDetails$.am!=null" class="col-md-3" style="font-size: 0.8em;">Αριθμός Μητρώου</div>
                        <div *ngIf="GelStudentDetails$.am!='' && GelStudentDetails$.am!=null" class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{GelStudentDetails$.am}}</div>
                    </div>

                    <div class="row evenin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div class="col-md-12" style="font-size: 1em; font-weight: bold; text-align: center">Στοιχεία Επικοινωνίας</div>
                    </div>

                    <div *ngIf="(GelStudentDetails$.lastschool_class !== '' && GelStudentDetails$.lastschool_schoolyear < '2013-2014') || (wsEnabled | async)===0 || GelStudentDetails$.lastschool_unittypeid =='40'" class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div class="col-md-3" style="font-size: 0.8em;">Διεύθυνση</div>
                        <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{GelStudentDetails$.regionaddress}}</div>
                        <div class="col-md-3" style="font-size: 0.8em;">ΤΚ - Πόλη</div>
                        <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{GelStudentDetails$.regiontk}} - {{GelStudentDetails$.regionarea}}</div>
                    </div>

                    <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div class="col-md-3" style="font-size: 0.8em;">Δήλωση από:</div>
                        <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{ GelStudentDetails$.relationtostudent }}</div>
                        <div class="col-md-3" style="font-size: 0.8em;">Τηλέφωνο Επικοινωνίας</div>
                        <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{GelStudentDetails$.telnum}}</div>
                    </div>

                    <div class="row evenin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div class="col-md-12" style="font-size: 1em; font-weight: bold; text-align: center">Επιλογές Μαθητή</div>
                    </div>

                    <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div *ngIf="GelStudentDetails$.nextclass !== ''" class="col-md-3" style="font-size: 0.8em;">Τάξη φοίτησης για το νέο σχολικό έτος</div>
                        <div *ngIf="GelStudentDetails$.nextclass === '1'" class="col-md-9" style="font-size: 0.8em; font-weight: bold">Α τάξη - ΗΜΕΡΗΣΙΟ ΓΕ.Λ.</div>
                        <div *ngIf="GelStudentDetails$.nextclass === '2'" class="col-md-9" style="font-size: 0.8em; font-weight: bold">Β τάξη - ΗΜΕΡΗΣΙΟ ΓΕ.Λ.</div>
                        <div *ngIf="GelStudentDetails$.nextclass === '3'" class="col-md-9" style="font-size: 0.8em; font-weight: bold">Γ τάξη - ΗΜΕΡΗΣΙΟ ΓΕ.Λ.</div>
                        <div *ngIf="GelStudentDetails$.nextclass === '4'" class="col-md-9" style="font-size: 0.8em; font-weight: bold">Α τάξη - ΕΣΠΕΡΙΝΟ ΓΕ.Λ.</div>
                        <div *ngIf="GelStudentDetails$.nextclass === '5'" class="col-md-9" style="font-size: 0.8em; font-weight: bold">Β τάξη - ΕΣΠΕΡΙΝΟ ΓΕ.Λ.</div>
                        <div *ngIf="GelStudentDetails$.nextclass === '6'" class="col-md-9" style="font-size: 0.8em; font-weight: bold">Γ τάξη - ΕΣΠΕΡΙΝΟ ΓΕ.Λ.</div>
                        <div *ngIf="GelStudentDetails$.nextclass === '7'" class="col-md-9" style="font-size: 0.8em; font-weight: bold">Δ τάξη - ΕΣΠΕΡΙΝΟ ΓΕ.Λ.</div>
                    </div>

                    <div *ngFor="let gelChoices$ of GelStudentDetails$['gelStudentChoices'];" class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div *ngIf="gelChoices$.choice_type === 'ΟΠ' " class="col-md-3" style="font-size: 0.8em;">Ομαδα Προσανατολισμου</div>
                        <div *ngIf="gelChoices$.choice_type === 'ΟΠ' " class="col-md-9" style="font-size: 0.8em; font-weight: bold">{{gelChoices$.choice_name}}</div>
                    </div>


                    <div *ngIf="GelStudentDetails$.nextclass==='1' || GelStudentDetails$.nextclass==='4' " class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div class="col-md-6" style="font-size: 0.8em; font-weight: bold;">Ξένη Γλώσσα:</div>
                    </div>
                    <div *ngIf="GelStudentDetails$.nextclass==='1' || GelStudentDetails$.nextclass==='4' " class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div class="col-md-2" style="font-size: 0.8em;"></div>
                        <div class="col-md-4" style="font-size: 0.8em; text-align: center;">Σειρά Προτίμησης</div>
                        <div class="col-md-6" style="font-size: 0.8em;">Τίτλος Μαθήματος</div>
                    </div>
                    <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;" *ngFor="let gelChoices$  of GelStudentDetails$['gelStudentChoices']; let i=index; let isOdd=odd; let isEven=even" [hidden]="UserData$.id !== applicationGelIdActive">
                        <div *ngIf="gelChoices$.choice_type === 'ΞΓ' " class="col-md-2" style="font-size: 0.8em;"></div>
                        <div *ngIf="gelChoices$.choice_type === 'ΞΓ' " class="col-md-4" style="font-size: 0.8em; font-weight: bold; text-align: center;">{{gelChoices$.order_id}}</div>
                        <div *ngIf="gelChoices$.choice_type === 'ΞΓ' " class="col-md-6" style="font-size: 0.8em; font-weight: bold;">{{gelChoices$.choice_name}}</div>
                    </div>


                    <div *ngIf="GelStudentDetails$.nextclass==='1' || GelStudentDetails$.nextclass==='3' || GelStudentDetails$.nextclass==='4' " class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div class="col-md-6" style="font-size: 0.8em; font-weight: bold;">Μάθημα Επιλογης:</div>
                    </div>
                    <div *ngIf="GelStudentDetails$.nextclass==='1' || GelStudentDetails$.nextclass==='3' || GelStudentDetails$.nextclass==='4' " class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div class="col-md-2" style="font-size: 0.8em;"></div>
                        <div class="col-md-4" style="font-size: 0.8em; text-align: center;">Σειρά Προτίμησης</div>
                        <div class="col-md-6" style="font-size: 0.8em;">Τίτλος Μαθήματος</div>
                    </div>
                    <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;" *ngFor="let gelChoices$  of GelStudentDetails$['gelStudentChoices']; let i=index; let isOdd=odd; let isEven=even" [hidden]="UserData$.id !== applicationGelIdActive">
                        <div *ngIf="gelChoices$.choice_type === 'ΕΠΙΛΟΓΗ' " class="col-md-2" style="font-size: 0.8em;"></div>
                        <div *ngIf="gelChoices$.choice_type === 'ΕΠΙΛΟΓΗ' " class="col-md-4" style="font-size: 0.8em; font-weight: bold; text-align: center;">{{gelChoices$.order_id}}</div>
                        <div *ngIf="gelChoices$.choice_type === 'ΕΠΙΛΟΓΗ' " class="col-md-6" style="font-size: 0.8em; font-weight: bold;">{{gelChoices$.choice_name}}</div>
                    </div>


                    <div class="row" style="margin-top: 20px; margin-bottom: 20px;">
                        <div class="col-md-12">
                            <!--<div *ngIf = "(GelStudentDetails$.status == '0' || GelStudentDetails$.status == '4') && GelStudentDetails$.applicantsAppModifyDisabled == '0'" >-->
                            <div *ngIf = "(GelStudentDetails$.canedit == '1') " >
                                <button type="button" class="btn-primary btn-lg pull-left isclickable" style="width: 10em;" (click)="editGelApplication()">
                                    <span style="font-size: 0.9em; font-weight: bold;">Επεξεργασία&nbsp;&nbsp;&nbsp;</span>
                                </button>
                            </div>
                            <button type="button" class="btn-primary btn-lg pull-right isclickable" style="width: 10em;" (click)="createGelPdfServerSide()">
                                <span style="font-size: 0.9em; font-weight: bold;">Εκτύπωση(PDF)&nbsp;&nbsp;&nbsp;</span>
                            </button>
                        </div>
                    </div>

                </div>
                </div>

            </div>
        </div>



        <div *ngIf="(SubmitedApplic$ | async).length > 0">
            <div class="row list-group-item isclickable"  style="margin: 0px 2px 0px 2px;" [class.oddout]="isOdd" [class.evenout]="isEven" [class.selectedappout]="applicationEpalIdActive === UserData$.id"
            *ngFor="let UserData$  of SubmitedApplic$ | async; let i=index; let isOdd=odd; let isEven=even" >

                <!--<div *ngIf = "(EpalSubmittedDetails$ | async).size">-->

                <div class="col-md-5" style="font-size: 0.8em; font-weight: bold;" (click)="setActiveEpalUser(UserData$.id)">{{UserData$.studentsurname}}</div>
                <div class="col-md-4" style="font-size: 0.8em; font-weight: bold;" (click)="setActiveEpalUser(UserData$.id)">{{UserData$.name}}</div>
                <div class="col-md-2" style="font-size: 0.8em; font-weight: bold;" (click)="setActiveEpalUser(UserData$.id)">{{UserData$.logo}}</div>
                <div *ngIf="UserData$.candelete === 1" class="col-md-1 text-right" style="font-size: 1em; font-weight: bold;"><i class="fa fa-trash isclickable" (click)="deleteApplication(UserData$.id)"></i></div>
                <div *ngIf="UserData$.candelete === 0" class="col-md-1" style="font-size: 1em; font-weight: bold;">&nbsp;</div>

                <div style="width: 100%">
                <div *ngFor="let StudentDetails$  of EpalSubmittedDetails$ | async" [hidden]="UserData$.id !== applicationEpalIdActive" style="margin: 10px 10px 10px 10px;">

                    <div *ngIf = "StudentDetails$.applicantsResultsDisabled == '0'  && !(showLoader$ | async)" >
                        <div *ngIf = "StudentDetails$.status == '1'" >
                            <div class="col-md-12" style="font-size: 1.0em; color: #21610B; font-weight: bold;">
                                Η αίτησή σας ικανοποιήθηκε. Έχετε επιλεγεί για να εγγραφείτε στο {{StudentDetails$.schoolName}}.
                                Παρακαλείστε να προσέλθετε ΑΜΕΣΑ στο σχολείο για να προχωρήσει η διαδικασία εγγραφής σας σε αυτό, προσκομίζοντας τα απαραίτητα δικαιολογητικά. Διεύθυνση σχολείου: {{StudentDetails$.schoolAddress}}, Τηλέφωνο σχολείου: {{StudentDetails$.schoolTel}}.<br><br>
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
                        <div *ngIf = "StudentDetails$.status == '5' " >
                            <div class="col-md-12" style="font-size: 1.0em; color: #a52a2a; font-weight: bold;">
                            Δεν ήταν δυνατή η τοποθέτησή σας σε σχολείο λόγω του αποτελέσματος φοίτησης.
                            Μπορείτε να προχωρήσετε σε τροποποίηση της αίτησης στη Β’ περίοδο αιτήσεων.<br><br>
                            </div>
                        </div>
                        <div *ngIf = "StudentDetails$.status == '6' " >
                            <div class="col-md-12" style="font-size: 1.0em; color: #a52a2a; font-weight: bold;">
                            Δεν ήταν δυνατή η τοποθέτησή σας σε σχολείο λόγω του τρέχοντος αποτελέσματος προαγωγής.
                            Σε περίπτωση προαγωγής σας τις αμέσως επόμενες ημέρες θα τοποθετηθείτε σε σχολείο χωρίς καμία δική σας περαιτέρω ενέργεια.<br><br>
                            </div>
                        </div>
                        <div *ngIf = "StudentDetails$.status == '7' " >
                            <div class="col-md-12" style="font-size: 1.0em; color: #a52a2a; font-weight: bold;">
                            Το αποτέλεσμα της προαγωγής σας δεν έχει καταχωρισθεί στο πληροφοριακό σύστημα του σχολείου (myschool).
                            Παρακαλώ επικοινωνήστε με τον διευθυντή του σχολείου σας.<br><br>
                            </div>
                        </div>
                    </div>


                    <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div class="col-md-3" style="font-size: 0.8em;">Αριθμός Δήλωσης Προτίμησης ΕΠΑΛ</div>
                        <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{StudentDetails$.applicationId}}</div>
                        <div class="col-md-3" style="font-size: 0.8em;">Υποβλήθηκε</div>
                        <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{StudentDetails$.changed}}</div>
                    </div>
                    <div class="row evenin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div class="col-md-12" style="font-size: 1em; font-weight: bold; text-align: center;">Στοιχεία Αιτούμενου</div>
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

                    <div class="row evenin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div class="col-md-12" style="font-size: 1em; font-weight: bold; text-align: center;">Στοιχεία Φοίτησης Μαθητή</div>
                    </div>

                    <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                    <div class="col-md-3" style="font-size: 0.8em;">Σχολείο τελευταίας φοίτησης</div>
                    <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{StudentDetails$.lastschool_schoolname}}</div>
                    <div class="col-md-3" style="font-size: 0.8em;">Σχολικό έτος τελευταίας φοίτησης</div>
                    <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{StudentDetails$.lastschool_schoolyear}}</div>
                    </div>

                    <div *ngIf="(StudentDetails$.lastschool_class !== '' && StudentDetails$.lastschool_schoolyear < '2013-2014') || (wsEnabled | async)===0 || StudentDetails$.lastschool_unittypeid =='40'" class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div *ngIf="StudentDetails$.lastschool_class !== ''" class="col-md-3" style="font-size: 0.8em;">Τάξη τελευταίας φοίτησης</div>
                        <div *ngIf="StudentDetails$.lastschool_class === '1'" class="col-md-9" style="font-size: 0.8em; font-weight: bold">Α</div>
                        <div *ngIf="StudentDetails$.lastschool_class === '2'" class="col-md-9" style="font-size: 0.8em; font-weight: bold">Β</div>
                        <div *ngIf="StudentDetails$.lastschool_class === '3'" class="col-md-9" style="font-size: 0.8em; font-weight: bold">Γ</div>
                        <div *ngIf="StudentDetails$.lastschool_class === '4'" class="col-md-9" style="font-size: 0.8em; font-weight: bold">Δ</div>
                    </div>

                    <div class="row evenin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div class="col-md-12" style="font-size: 1em; font-weight: bold; text-align: center;">Προσωπικά Στοιχεία Μαθητή</div>
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
                        <div *ngIf="StudentDetails$.am!='' && StudentDetails$.am!=null" class="col-md-3" style="font-size: 0.8em;">Αριθμός Μητρώου</div>
                        <div *ngIf="StudentDetails$.am!='' && StudentDetails$.am!=null" class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{StudentDetails$.am}}</div>
                    </div>

                    <div class="row evenin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div class="col-md-12" style="font-size: 1em; font-weight: bold; text-align: center;">Στοιχεία Επικοινωνίας</div>
                    </div>

                    <div *ngIf="(StudentDetails$.lastschool_class !== '' && StudentDetails$.lastschool_schoolyear < '2013-2014') || (wsEnabled | async)===0 || StudentDetails$.lastschool_unittypeid =='40'" class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div class="col-md-3" style="font-size: 0.8em;">Διεύθυνση</div>
                        <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{StudentDetails$.regionaddress}}</div>
                        <div class="col-md-3" style="font-size: 0.8em;">ΤΚ - Πόλη</div>
                        <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{StudentDetails$.regiontk}} - {{StudentDetails$.regionarea}}</div>
                    </div>

                    <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
                        <div class="col-md-3" style="font-size: 0.8em;">Δήλωση από:</div>
                        <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{ StudentDetails$.relationtostudent }}</div>
                        <div class="col-md-3" style="font-size: 0.8em;">Τηλέφωνο Επικοινωνίας</div>
                        <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{StudentDetails$.telnum}}</div>
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

                    <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;" *ngFor="let epalChoices$  of StudentDetails$['epalSchoolsChosen']; let i=index; let isOdd=odd; let isEven=even" [hidden]="UserData$.id !== applicationEpalIdActive">
                        <div class="col-md-6" style="font-size: 0.8em; font-weight: bold; text-align: center;">{{epalChoices$.choice_no}}</div>
                        <div class="col-md-6" style="font-size: 0.8em; font-weight: bold;">{{epalChoices$.epal_id}}</div>
                    </div>

                    <div class="row" style="margin-top: 20px; margin-bottom: 20px;">
                        <div class="col-md-12">
                          <!--
                          ενεργοποίηση Τροποποίησης Αίτησης όταν: δεν υπάρχει αποτέλεσμα κατανομής για αυτήν την αίτηση
                          ΚΑΙ επιτρέπεται η τροποποίηση αιτήσεων
                          -->
                            <div *ngIf = "(StudentDetails$.status == '3' || StudentDetails$.status == '4' || StudentDetails$.status == '0') && StudentDetails$.applicantsAppModifyDisabled == '0'" >
                                <button type="button" class="btn-primary btn-lg pull-left isclickable" style="width: 10em;" (click)="editEpalApplication()">
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

   `
})

@Injectable() export default class SubmitedPreview implements OnInit, OnDestroy {

    private SubmitedApplic$: BehaviorSubject<any>;
    private SubmitedUsersSub: Subscription;
    private EpalSubmittedDetails$: BehaviorSubject<any>;
    private SubmitedDetailsSub: Subscription;

    private gelclassesSub: Subscription;
    private electivecourseFieldsSub: Subscription;
    private langcourseFieldsSub: Subscription;
    private OrientationGroupSub: Subscription;
    private sectorFieldsSub: Subscription;
    private regionsSub: Subscription;
    private sectorsSub: Subscription;

    private showLoader$: BehaviorSubject<boolean>;
    private isModalShown: BehaviorSubject<boolean>;
    private applicationEpalIdActive = <number>-1;
    private applicationGelIdActive = <number>-1;

    private GelSubmittedApplic$: BehaviorSubject<any>;
    private SubmittedGelUsersSub: Subscription;
    private GelSubmittedDetails$: BehaviorSubject<any>;
    private GelSubmittedDetailsSub: Subscription;

    private applicationId = <number>0;
    //private applicationGelId = <number>0;
    private schooltype: string;

    //private wsIdentSub: Subscription;
    private wsEnabled:  BehaviorSubject<number>;
    private loginInfoSub: Subscription;

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
        private _lcfa: LangCourseFieldsActions,
        private _ogs: OrientationGroupActions,
        private _gsdf: GelStudentDataFieldsActions,
        private _hds: HelperDataService,
        private activatedRoute: ActivatedRoute,
        private router: Router,
        private loc: Location
    ) {
        this.SubmitedApplic$ = new BehaviorSubject([{}]);
        this.EpalSubmittedDetails$ = new BehaviorSubject([{}]);
        this.showLoader$ = new BehaviorSubject(false);
        this.isModalShown = new BehaviorSubject(false);

        this.GelSubmittedApplic$ = new BehaviorSubject([{}]);
        this.GelSubmittedDetails$=new BehaviorSubject([{}]);
        this.wsEnabled = new BehaviorSubject(-1);

    }

    ngOnDestroy() {
        (<any>jQuery("#applicationDeleteConfirm")).remove();
        (<any>jQuery("#applicationDeleteError")).remove();
        if (this.SubmitedUsersSub)
            this.SubmitedUsersSub.unsubscribe();
        if (this.SubmitedDetailsSub)
            this.SubmitedDetailsSub.unsubscribe();
        if (this.gelclassesSub)
            this.gelclassesSub.unsubscribe();
        if (this.electivecourseFieldsSub)
            this.electivecourseFieldsSub.unsubscribe();
        if (this.langcourseFieldsSub)
            this.langcourseFieldsSub.unsubscribe();
        if (this.OrientationGroupSub)
            this.OrientationGroupSub.unsubscribe();
        if (this.sectorFieldsSub)
            this.sectorFieldsSub.unsubscribe();
        if (this.regionsSub)
            this.regionsSub.unsubscribe();
        if (this.sectorsSub)
            this.sectorsSub.unsubscribe();
        if (this.SubmittedGelUsersSub)
            this.SubmittedGelUsersSub.unsubscribe();
        if (this.GelSubmittedDetailsSub)
            this.GelSubmittedDetailsSub.unsubscribe();
        if (this.loginInfoSub)
            this.loginInfoSub.unsubscribe();
    }

    ngOnInit() {

        (<any>jQuery("#applicationDeleteConfirm")).appendTo("body");
        (<any>jQuery("#applicationDeleteError")).appendTo("body");
        this.showLoader$.next(true);

        //this.wsIdentSub = this._hds.isWS_ident_enabled().subscribe(z => {
        //    this.wsEnabled.next(Number(z.res)) ;
        //});

        this.resetStore();

        this.SubmitedUsersSub = this._hds.getSubmittedPreview().subscribe(
            data => {
                this.SubmitedApplic$.next(data);
            },
            error => {
                this.SubmitedApplic$.next([{}]);
                this.showLoader$.next(false);
                console.log("Error Getting Schools");
            });

        this.SubmittedGelUsersSub = this._hds.getGelSubmittedPreview().subscribe(
            data => {
                this.GelSubmittedApplic$.next(data);
                this.showLoader$.next(false);
            },
            error => {
                this.GelSubmittedApplic$.next([{}]);
                this.showLoader$.next(false);
                console.log("Error Getting Schools");
            });

        this.loginInfoSub = this._ngRedux.select("loginInfo")
           .map(loginInfo => <ILoginInfoRecords>loginInfo)
           .subscribe(linfo => {
             //new piece of code
             if (linfo.size > 0) {
                 linfo.reduce(({ }, loginInfoObj) => {
                     this.wsEnabled.next(loginInfoObj.ws_ident);
                     return loginInfoObj;
                 }, {});
               }
               //end new piece of code
               //this.loginInfo$.next(linfo);
         }, error => { console.log("error selecting loginInfo"); });

    }


    setActiveEpalUser(ind: number): void {


        this.resetStore();
        if (ind === this.applicationEpalIdActive) {
            this.applicationEpalIdActive = 0;
            this.applicationGelIdActive = 0;
            return;
        }

        this.applicationEpalIdActive = ind;
        this.applicationGelIdActive = 0;

        this.SubmitedDetailsSub = this._hds.getStudentDetails(this.applicationEpalIdActive).subscribe(data => {
            this.EpalSubmittedDetails$.next(data);
            this.createStoreWithEpalAppData();
        },
            error => {
                this.EpalSubmittedDetails$.next([{}]);
                console.log("Error Getting Schools");
            });


    }

    setActiveGelUser(ind: number): void {

        this.resetStore();

        if (ind === this.applicationGelIdActive) {
            this.applicationGelIdActive = 0;
            this.applicationEpalIdActive = 0;
            return;
        }

        this.applicationGelIdActive = ind;
        this.applicationEpalIdActive = 0;
        this.showLoader$.next(true);

        this.GelSubmittedDetailsSub = this._hds.getGelStudentDetails(this.applicationGelIdActive).subscribe(data => {
            this.GelSubmittedDetails$.next(data);
            this.createStoreWithGelAppData();
            this.showLoader$.next(false);
        },
            error => {
                this.GelSubmittedDetails$.next([{}]);
                console.log("Error Getting Schools");
                this.showLoader$.next(false);
            });


    }


    resetStore() {

      this._eca.initEpalClasses();
      this._cfa.initDataMode();
      this._sdfa.initStudentDataFields();
      this._sfa.initSectorFields();
      this._rsa.initRegionSchools();
      this._csa.initSectorCourses();
      this._sta.initSchoolType();
      this._gca.initGelClasses();
      this._ecf.initElectiveCourseFields();
      this._lcfa.initLangCourseFields();
      this._ogs.initOrientationGroup();
      this._gsdf.initGelStudentDataFields();
    }

    createPdfServerSide() {
        this._hds.createPdfServerSide(this.applicationEpalIdActive, this.EpalSubmittedDetails$.getValue()[0].status,
          this.EpalSubmittedDetails$.getValue()[0].schoolName, this.EpalSubmittedDetails$.getValue()[0].schoolAddress, this.EpalSubmittedDetails$.getValue()[0].schoolTel);
    }

    createGelPdfServerSide() {
        this._hds.createGelPdfServerSide(this.applicationGelIdActive, this.GelSubmittedDetails$.getValue()[0].status,
          this.GelSubmittedDetails$.getValue()[0].schoolName, this.GelSubmittedDetails$.getValue()[0].schoolAddress, this.GelSubmittedDetails$.getValue()[0].schoolTel);
    }

    deleteApplication(appId: number): void {
        this.applicationId = appId;
        this.schooltype = "epal";
        this.showConfirmModal();
    }

    deleteGelApplication(appId: number): void {
        this.applicationId = appId;
        this.schooltype = "gel";
        this.showConfirmModal();
    }


    deleteApplicationDo(): void {
        this.hideConfirmModal();
        this.showLoader$.next(true);
        this.resetStore();
        this._hds.deleteApplication(this.applicationId, this.schooltype).then(data => {
            if (this.schooltype === "epal") {
              this.SubmitedUsersSub.unsubscribe();
              this.SubmitedUsersSub = this._hds.getSubmittedPreview().subscribe(
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
              else if (this.schooltype === "gel") {
                this.SubmittedGelUsersSub.unsubscribe();
                this.SubmittedGelUsersSub = this._hds.getGelSubmittedPreview().subscribe(
                    data => {
                        this.GelSubmittedApplic$.next(data);
                        this.showLoader$.next(false);
                    },
                    error => {
                        this.GelSubmittedApplic$.next([{}]);
                        this.showLoader$.next(false);
                        console.log("Error Getting Schools");
                    });
                }

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


    editEpalApplication() {

      this._cfa.saveDataModeSelected({
          app_update: true, appid: this.EpalSubmittedDetails$.getValue()[0].applicationId, apptype: "epal",
          sector_id: this.EpalSubmittedDetails$.getValue()[0].currentsector_id, course_id: this.EpalSubmittedDetails$.getValue()[0].currentcourse_id,
          epal_choice: this.EpalSubmittedDetails$.getValue()[0].epalSchoolsChosen, currentclass: this.EpalSubmittedDetails$.getValue()[0].currentclass
        });

      this.router.navigate(["/epal-class-select"]);
    }

    createStoreWithEpalAppData()  {

        /*
        this._cfa.saveDataModeSelected({
            app_update: true, appid: this.EpalSubmittedDetails$.getValue()[0].applicationId, apptype: "epal",
            sector_id: this.EpalSubmittedDetails$.getValue()[0].currentsector_id, course_id: this.EpalSubmittedDetails$.getValue()[0].currentcourse_id,
            epal_choice: this.EpalSubmittedDetails$.getValue()[0].epalSchoolsChosen, currentclass: this.EpalSubmittedDetails$.getValue()[0].currentclass
          });
        */

        let birthdate = this.EpalSubmittedDetails$.getValue()[0].birthdate;
        let birthparts = birthdate.split("/",3);
        this._sdfa.saveStudentDataFields([{
            am: this.EpalSubmittedDetails$.getValue()[0].am,
            name: this.EpalSubmittedDetails$.getValue()[0].name,
            studentsurname: this.EpalSubmittedDetails$.getValue()[0].studentsurname,
            fatherfirstname: this.EpalSubmittedDetails$.getValue()[0].fatherfirstname,
            motherfirstname: this.EpalSubmittedDetails$.getValue()[0].motherfirstname,
            regionaddress: this.EpalSubmittedDetails$.getValue()[0].regionaddress,
            regiontk: this.EpalSubmittedDetails$.getValue()[0].regiontk,
            regionarea: this.EpalSubmittedDetails$.getValue()[0].regionarea,
            lastschool_schoolname: {registry_no: this.EpalSubmittedDetails$.getValue()[0].lastschool_registrynumber,
            name: this.EpalSubmittedDetails$.getValue()[0].lastschool_schoolname,
            unit_type_id: this.EpalSubmittedDetails$.getValue()[0].lastschool_unittypeid},
            lastschool_schoolyear: this.EpalSubmittedDetails$.getValue()[0].lastschool_schoolyear,
            lastschool_class: this.EpalSubmittedDetails$.getValue()[0].lastschool_class,
            relationtostudent: this.EpalSubmittedDetails$.getValue()[0].relationtostudent,
            telnum: this.EpalSubmittedDetails$.getValue()[0].telnum,
            studentbirthdate: {date: {year: Number(birthparts[2]),
                month: Number(birthparts[1]),
                day: Number(birthparts[0])}}
        }]);

        this._sta.saveSchoolTypeSelected(2, "ΕΠΑΛ");
        this._eca.saveEpalClassesSelected({name: this.EpalSubmittedDetails$.getValue()[0].currentclass});
        let class_id = this.EpalSubmittedDetails$.getValue()[0].currentclass;

        if (class_id === "2" ){
            this.showLoader$.next(true);
            this._sfa.getSectorFields(false).then(()=>{
                this._sfa.saveSectorFieldsSelectedwithIds(-1, this.EpalSubmittedDetails$.getValue()[0].currentsector_id );
                this.showLoader$.next(false);
            });
        }
        else if (class_id === "3" || class_id === "4" ){
            this.showLoader$.next(true);
            this._csa.getSectorCourses(false).then(()=>{
                this._csa.saveSectorCoursesSelectedwithIds(-1, -1, true, this.EpalSubmittedDetails$.getValue()[0].currentsector_id, this.EpalSubmittedDetails$.getValue()[0].currentcourse_id );
                this.showLoader$.next(false);
            });
        }

        if (class_id === "1" ){
            this.showLoader$.next(true);
            this._rsa.getRegionSchools(parseInt(class_id), "-1", true, false).then(()=>{
                this.showLoader$.next(true);
                for (let k=0; k < (this.EpalSubmittedDetails$.getValue()[0].epalSchoolsChosen).length; k++)  {
                    this._rsa.saveRegionSchoolsSelectedwithIds(true, this.EpalSubmittedDetails$.getValue()[0].epalSchoolsChosen[k].id, this.EpalSubmittedDetails$.getValue()[0].epalSchoolsChosen[k].choice_no) ;
                }
                this.showLoader$.next(false);
            });
        }
        else if (class_id === "2" ) {
            this.showLoader$.next(true);
            this._rsa.getRegionSchools(parseInt(class_id), parseInt(this.EpalSubmittedDetails$.getValue()[0].currentsector_id), true, false).then(()=>{
                this.showLoader$.next(true);
                for (let k=0; k < (this.EpalSubmittedDetails$.getValue()[0].epalSchoolsChosen).length; k++)  {
                    this._rsa.saveRegionSchoolsSelectedwithIds(true, this.EpalSubmittedDetails$.getValue()[0].epalSchoolsChosen[k].id, this.EpalSubmittedDetails$.getValue()[0].epalSchoolsChosen[k].choice_no) ;
                }
                this.showLoader$.next(false);
            });
        }
        else if (class_id === "3" || class_id === "4" ){
            this.showLoader$.next(true);
            //console.log(this.EpalSubmittedDetails$.getValue()[0].currentcourse_id);
            this._rsa.getRegionSchools(parseInt(class_id), parseInt(this.EpalSubmittedDetails$.getValue()[0].currentcourse_id), true, false).then(()=>{
                this.showLoader$.next(true);
                for (let k=0; k < (this.EpalSubmittedDetails$.getValue()[0].epalSchoolsChosen).length; k++)  {
                    this._rsa.saveRegionSchoolsSelectedwithIds(true, this.EpalSubmittedDetails$.getValue()[0].epalSchoolsChosen[k].id, this.EpalSubmittedDetails$.getValue()[0].epalSchoolsChosen[k].choice_no) ;
                }
                this.showLoader$.next(false);
            });
        }
    }



    editGelApplication()  {
        this._cfa.saveDataModeSelected({app_update: true, appid: this.GelSubmittedDetails$.getValue()[0].applicationId, apptype: "gel"});
        this.router.navigate(["/gel-class-select"]);
    }

    createStoreWithGelAppData()  {

        let birthdate = this.GelSubmittedDetails$.getValue()[0].birthdate;
        let birthparts = birthdate.split("/",3);
        this._gsdf.saveGelStudentDataFields([{
              am: this.GelSubmittedDetails$.getValue()[0].am,
              name: this.GelSubmittedDetails$.getValue()[0].name,
              studentsurname: this.GelSubmittedDetails$.getValue()[0].studentsurname,
              fatherfirstname: this.GelSubmittedDetails$.getValue()[0].fatherfirstname,
              motherfirstname: this.GelSubmittedDetails$.getValue()[0].motherfirstname,
              regionaddress: this.GelSubmittedDetails$.getValue()[0].regionaddress,
              regiontk: this.GelSubmittedDetails$.getValue()[0].regiontk,
              regionarea: this.GelSubmittedDetails$.getValue()[0].regionarea,
              lastschool_schoolname: {registry_no: this.GelSubmittedDetails$.getValue()[0].lastschool_registrynumber,
              name: this.GelSubmittedDetails$.getValue()[0].lastschool_schoolname,
              unit_type_id: this.GelSubmittedDetails$.getValue()[0].lastschool_unittypeid},
              lastschool_schoolyear: this.GelSubmittedDetails$.getValue()[0].lastschool_schoolyear,
              lastschool_class: this.GelSubmittedDetails$.getValue()[0].lastschool_class,
              relationtostudent: this.GelSubmittedDetails$.getValue()[0].relationtostudent,
              telnum: this.GelSubmittedDetails$.getValue()[0].telnum,
              studentbirthdate: {date: {year: Number(birthparts[2]),
                  month: Number(birthparts[1]),
                  day: Number(birthparts[0])}}
            }]);

        //this._cfa.saveDataModeSelected({app_update: true, appid: this.GelSubmittedDetails$.getValue()[0].applicationId, apptype: "gel"});
        this._sta.saveSchoolTypeSelected(1, "ΓΕΛ");

        this.showLoader$.next(true);
        this._gca.getClassesList(false).then(()=>{
            this.showLoader$.next(true);
            this._gca.saveGelClassesSelectedwithIds(0, this.GelSubmittedDetails$.getValue()[0].nextclass);
            this.showLoader$.next(false);
        });

        let class_id = parseInt(this.GelSubmittedDetails$.getValue()[0].nextclass);
        if (class_id === 1 || class_id === 3 || class_id === 4){
            this.showLoader$.next(true);
            this._ecf.getElectiveCourseFields(false,class_id).then(()=>{
                this.showLoader$.next(true);
                for (let k=0; k < (this.GelSubmittedDetails$.getValue()[0].gelStudentChoices).length; k++)  {
                    if ( this.GelSubmittedDetails$.getValue()[0].gelStudentChoices[k].choice_type === "ΕΠΙΛΟΓΗ") {
                        this._ecf.saveElectiveCourseFieldsSelectedwithIds(this.GelSubmittedDetails$.getValue()[0].gelStudentChoices[k].choice_id, 0, this.GelSubmittedDetails$.getValue()[0].gelStudentChoices[k].order_id);

                    }
                }
                this.showLoader$.next(false);
            });
        }


        //new code
        if (class_id === 1 || class_id === 4){
            this.showLoader$.next(true);
            this._lcfa.getLangCourseFields(false,class_id).then(()=>{
                this.showLoader$.next(true);
                for (let k=0; k < (this.GelSubmittedDetails$.getValue()[0].gelStudentChoices).length; k++)  {
                    if ( this.GelSubmittedDetails$.getValue()[0].gelStudentChoices[k].choice_type === "ΞΓ") {
                        this._lcfa.saveLangCourseFieldsSelectedwithIds(this.GelSubmittedDetails$.getValue()[0].gelStudentChoices[k].choice_id, 0, this.GelSubmittedDetails$.getValue()[0].gelStudentChoices[k].order_id);

                    }
                }
                this.showLoader$.next(false);
            });
        }
        //end new code

        if (class_id === 2 || class_id === 3 || class_id === 6 || class_id === 7){
            this.showLoader$.next(true);
            this._ogs.getOrientationGroups(false,class_id,"ΟΠ").then(()=>{
                this.showLoader$.next(true);
                for (let k=0; k < (this.GelSubmittedDetails$.getValue()[0].gelStudentChoices).length; k++)  {
                    if ( this.GelSubmittedDetails$.getValue()[0].gelStudentChoices[k].choice_type === "ΟΠ") {
                        this._ogs.saveOrientationGroupSelectedwithIds(this.GelSubmittedDetails$.getValue()[0].gelStudentChoices[k].choice_id, 0);
                    }
                }
                this.showLoader$.next(false);
             });
         }

    }



}
