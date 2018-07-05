import { Component, OnDestroy, OnInit } from "@angular/core";
import { Injectable } from "@angular/core";
import { BehaviorSubject, Observable, Subscription } from "rxjs/Rx";
import { HelperDataService } from "../../services/helper-data-service";
import { AppSettings } from "../../app.settings";

import { ILoginInfoRecords } from "../../store/logininfo/logininfo.types";
import { IAppState } from "../../store/store";
import { LOGININFO_INITIAL_STATE } from "../../store/logininfo/logininfo.initial-state";
import { NgRedux } from "@angular-redux/store";
import { Http, Headers, RequestOptions } from "@angular/http";

import {
    FormBuilder,
    FormGroup,
    FormControl,
    FormArray,
    Validators,
} from '@angular/forms';


@Component({
    selector: "gel-distribution",
    template: `

  <div id="informationfeedback" class="modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header {{modalHeader | async}}">
                    <h3 class="modal-title pull-left"><i class="fa fa-check-square-o"></i>&nbsp;&nbsp;{{ modalTitle | async }}</h3>
                    <button type="button" class="close pull-right" aria-label="Close" (click)="hideModal()">
                        <span aria-hidden="true"><i class="fa fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body"><p>{{ modalText | async }}</p></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal" (click)="hideModal()">Κλείσιμο</button>
                </div>
            </div>
        </div>
      </div>




    <div class = "loading" *ngIf="(showLoader | async) === true"></div>
    <div style="min-height: 500px;">
    <form [formGroup]="formGroup">


       <p *ngIf = "(showLoader | async) === false && hasdone === true" style="margin-top: 20px; line-height: 2em;">
         Επιλέξτε την τάξη για την οποία πρόκειται να τοποθετήσετε μαθητές με βάση τη διεύθυνση κατοικίας τους.
         Ειδικότερα για την Α' Λυκείου επιλέγοντας το Γυμνάσιο που επιθυμείται, εμφανίζονται ο Α/Α και η διεύθυνση κατοικίας των αιτήσεων των αντίστοιχων μαθητών.
         Για να τοποθετήσετε τους μαθητές με βάση τη διεύθυνση κατοικίας τους, επιλέξτε μία ή περισσότερες αιτήσεις και την απαιτούμενη Σχολική Μονάδα Τοποθέτησης.
       </p>

       <button type="button" *ngIf="(showLoader | async) === false && hasdone === false" class="btn-primary btn-lg isclickable" style="width: 9em;"  (click)="initialaizattion()" >
           Αρχικοποίηση
        </button>

       <p *ngIf="(showLoader | async) === false && hasdone === false" style="margin-top: 20px; line-height: 2em;"> Είναι η <strong>πρώτη</strong> φορά που εισέρχεστε στην επιλογή Τοποθέτησης Μαθητών.
          Για να προχωρήσετε, παρακαλώ επιλέξτε <strong>Αρχικοποίηση</strong>.
          Η λειτουργία της αρχικοποίησης δεν θα σας ξαναζητηθεί την επόμενη φορά που θα εισέλθετε στην επιλογή αυτή.
       </p>

       <!--
       <p *ngIf="hasdone === true" style="margin-top: 20px; line-height: 2em;">
         Επιλέξτε την τάξη για την οποία πρόκειται να τοποθετήσετε μαθητές με βάση τη διεύθυνση κατοικίας τους.
         Στην παρακάτω λίστα εμφανίζονται τα Γυμνάσια της περιοχής ευθύνης σας. Παρακαλώ
       επιλέξτε σχολείο για να τοποθετήσετε τους μαθητές με βάση τη διεύθυνση κατοικίας τους στο αντίστοιχο λύκειο.</p>
       -->

       <li class="list-group-item isclickable" (click)="setActiveclass(1)">
          <div *ngIf="hasdone === true" class="col-md-12" style="font-weight: bold;"  [class.selectedout]="aclassActive === 1"

          (click)="setActiveRegion(0,1,1,0,addressfilter, amfilter)"  > Α' Λυκείου

          </div>
       </li>
      <div [hidden] ="aclassActive !== 1 ">
      <div class="row" style="margin-top: 20px; line-height: 2em;" > <b> Επιλέξτε Γυμνάσιο Προέλευσης</b></div>
            <div class="col-md-11 offset-md-1">

                <select #secsel class="form-control" formControlName="secsel"
                        (change)="setActiveRegion(secsel,1,1,0,addressfilter, amfilter)">
                    <option value="0"></option>
                    <option *ngFor="let JuniorHighSchools$  of JuniorHighSchool$ | async; let i=index; let isOdd=odd; let isEven=even" [value]="JuniorHighSchools$.id"> {{JuniorHighSchools$.name}}</option>
                    <option value="5000">Υπόλοιπες Περιπτώσεις</option>

                </select>
                </div>
           <br>
           <br>
           <div [hidden] ="regionActive === -1">
             <p style="margin-top: 20px; line-height: 2em;"> Σε περίπτωση που θέλετε να εφαρμόσετε φίλτρο στα αποτελέσματα συμπληρώστε το αντιστοιχο πεδίο και πατήστε εφαρμογή</p>
             <div class="col-md-12" style="font-weight: bold;"> Φίλτρα </div>
            <div class="row form-group">
              <div class="col-4">
              <label for="addressfilter">
                Διεύθυνση Κατοικίας:
              </label>
                  <input #addressfilter type= "text" class="form-control" formControlName="addressfilter">
              </div>
              <div class="col-3">
              <label for="amfilter">
                ΑΜ Μαθητή:
              </label>
                  <input #addressfilter type= "text" class="form-control"  formControlName="amfilter">
               </div>
               <div class="col-2">
               <label>
                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              </label>
                <button type="button" class="btn-primary btn-sm  isclickable" style="width: 9em;"  (click)="setActiveRegion(secsel,1,0,0,addressfilter, amfilter)">
                     Αναζήτηση
                </button>
                </div>
               <div class="col-3">
              <label>
                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              </label>
                <button type="button" class="btn-primary btn-sm  isclickable" style="width: 9em;" (click)="deletefilters(secsel,1)" >
                     Καθαρισμός φίλτρου
                </button>

                </div>
           </div>




           <div class="list-group-item framecolor">
                    <div class="col-md-1" style="   font-weight: bold; font-size: 0.8em" >Επιλογή Όλων
                       <input #so type="checkbox" [checked]="selall ===  true" (change)="selectall(1)">
                    </div>
                    <div class="col-md-4" style="   font-weight: bold; font-size: 0.8em" >
                      <div>A/A Αίτησης/ Α.Μ. Μαθητή</div>
                      <div>Διεύθυνση Κατοικίας </div>

                    </div>
                    <div class="col-md-2 " style="   font-weight: bold; font-size: 0.8em" >Τύπος Σχολείου</div>
                    <div class="col-md-3 " style="font-weight: bold;">
                            Σχολείο Τοποθέτησης
                     </div>
                    <div class="col-md-2 ">
                    </div>
                 </div>


                 <div *ngFor="let AllStudents$  of StudentsPerSchool$ | async; let l=index; let isOdd=odd; let isEven=even"
                  class="row list-group-item isclickable" [class.oddout]="isOdd" [class.evenout]="isEven"
                   style="margin: 0px 2px 0px 2px;"  [hidden]="calchidden(AllStudents$.idnew)" [class.changecolor]="AllStudents$.oldschool !== false">


                    <div class="col-md-1 " style = "font-size: 0.8em">
                     <input #cb type="checkbox" [checked]="findid(AllStudents$.id)" (change)="updateCheckedOptions(AllStudents$.id, l)">
                   </div>

                    <div class="col-md-4"  style = "font-size: 0.8em">

                      <div>{{AllStudents$.id}}/{{AllStudents$.am}}</div>
                      <div>{{AllStudents$.regionaddress}}</div>
                      <div>{{AllStudents$.regionarea}} </div>
                      <div>{{AllStudents$.regiontk}}</div>
                    </div>
                    <div class="col-md-2 " style = "font-size: 0.8em" >{{AllStudents$.school_type}}</div>
                    <div class="col-md-3 " style = "font-size: 0.8em" >
                      <div *ngIf="AllStudents$.oldschool !== false"  class= "changecolor" >
                              {{AllStudents$.oldschool}}
                       </div>

                       <div *ngIf="AllStudents$.oldschool !== true" >
                       </div>
                       </div>
                     <div  class="col-md-1"  style="font-size: 0.8em;">
                       <i *ngIf="AllStudents$.oldschool !== false" class="fa fa-undo isclickable" (click)="undosave(AllStudents$.id,1,addressfilter, amfilter)"></i>
                     </div>

       </div>


         <div style="font-weight: bold;">
         <div class="container framecolor">
         <span class="border border-info" >
          <div class="form-group" class="row" style = "font-weight: bold; font-size: 0.8em">
           <div class="col-3">
          Βρίσκεστε στη σελίδα:
          </div>
          <div class="col-1">
           <input #pageno type="text" class="form-control" placeholder=".col-1" formControlName="pageno">
          </div>
          <div class="col-1">
           απο
           </div>
           <div class="col-1">
           <input #maxpage type="text" class="form-control" placeholder=".col-1" formControlName="maxpage" style="width:50px">
           </div>

          <div class="col-4">
           Αριθμός μαθητών ανα σελίδα:
           </div>
          <div class="col-2">
           <select #studentperpage class="form-control"  formControlName="studentperpage" (change)= "changestudentsperpage(studentperpage,secsel,1)">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                         <option value="50">50</option>
                        <option value="100">100</option>


            </select>
            </div>
           </div>
             <br>
             <nav aria-label="pagination">
              <ul class="pagination justify-content-center">
                <li class="page-item " >
                  <button class="page-link"  (click)="prevpage(secsel,1)">Προηγούμενη</button>
                </li>
                <li class="page-item">
                  <button class="page-link"  (click) ="nextpage(secsel,1) ">Επόμενη</button>
                </li>
              </ul>

            </nav>
            </span>
         </div>


          <div style="width: 100%; color: #000000; font-weight: bold;" >
                 <p style="margin-top: 20px; line-height: 2em;"> Αφού έχετε επιλέξει τους μαθητες απο την παραπάνω λίστα, στη συνέχεια επιλέξτε το αντίστοιχο Λύκειο υποδοχής.</p>
                 <label> Λύκειο Υποδοχής: </label>
                   <select #highscsel1 class="form-control" (change)="confirmSchool(highscsel1,1,addressfilter, amfilter)" >
                        <option value="0"></option>
                        <option *ngFor="let HighSchools$  of HighSchool$ | async; let i=index"
                        [value] = "HighSchools$.id"> {{HighSchools$.name}} </option>
                   </select>
          </div>
                   <br>
                   <br>
        </div>

      </div>


      </div>




       <li class="list-group-item isclickable" (click)="setActiveclass(2)">
          <div *ngIf="hasdone === true" class="col-md-12" style="font-weight: bold;"  [class.selectedout]="aclassActive === 2"
           (click)="setActiveRegion(0,2,1,0,addressfilter, amfilter)"
          > Β' Λυκείου  </div>
       </li>
      <div [hidden] ="aclassActive !== 2">
           <br>
           <br>
           <div [hidden] ="regionActive === -1">
             <p style="margin-top: 20px; line-height: 2em;"> Σε περίπτωση που θέλετε να εφαρμόσετε φίλτρο στα αποτελέσματα συμπληρώστε το αντιστοιχο πεδίο και πατήστε εφαρμογή</p>
             <div class="col-md-12" style="font-weight: bold;"> Φίλτρα </div>
            <div class="row form-group">
              <div class="col-4">
              <label for="addressfilter">
                Διεύθυνση Κατοικίας:
              </label>
                  <input #addressfilter type= "text" class="form-control" formControlName="addressfilter">
              </div>
              <div class="col-3">
              <label for="amfilter">
                ΑΜ Μαθητή:
              </label>
                  <input #addressfilter type= "text" class="form-control"  formControlName="amfilter">
               </div>
               <div class="col-2">
               <label>
                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              </label>
                <button type="button" class="btn-primary btn-sm  isclickable" style="width: 9em;"  (click)="setActiveRegion(0,2,0,0,addressfilter, amfilter)">
                     Αναζήτηση
                </button>
                </div>
               <div class="col-3">
              <label>
                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              </label>
                <button type="button" class="btn-primary btn-sm  isclickable" style="width: 9em;" (click)="deletefilters(0,2)" >
                     Καθαρισμός φίλτρου
                </button>

                </div>
           </div>




           <div class="list-group-item framecolor">
                    <div class="col-md-1" style="   font-weight: bold; font-size: 0.8em" >Επιλογή Όλων
                       <input #so type="checkbox" [checked]="selall ===  true" (change)="selectall(2)">
                    </div>
                    <div class="col-md-4" style="   font-weight: bold; font-size: 0.8em" >
                      <div>A/A Αίτησης/ Α.Μ. Μαθητή</div>
                      <div>Διεύθυνση Κατοικίας </div>

                    </div>
                    <div class="col-md-2 " style="font-weight: bold; font-size: 0.8em" >Τύπος Σχολείου</div>
                    <div class="col-md-3 " style="font-weight: bold;">
                            Σχολείο Τοποθέτησης
                     </div>
                    <div class="col-md-2 ">
                    </div>
                 </div>


                 <div *ngFor="let AllStudents$  of StudentsPerSchool$ | async; let l=index; let isOdd=odd; let isEven=even"
                  class="row list-group-item isclickable" [class.oddout]="isOdd" [class.evenout]="isEven"
                   style="margin: 0px 2px 0px 2px;"  [hidden]="calchidden(AllStudents$.idnew)" [class.changecolor]="AllStudents$.oldschool !== false">


                    <div class="col-md-1 " style = "font-size: 0.8em">
                     <input #cb type="checkbox" [checked]="findid(AllStudents$.id)" (change)="updateCheckedOptions(AllStudents$.id, l)">
                   </div>

                    <div class="col-md-4"  style = "font-size: 0.8em">

                      <div>{{AllStudents$.id}}/{{AllStudents$.am}}</div>
                      <div>{{AllStudents$.regionaddress}}</div>
                      <div>{{AllStudents$.regionarea}} </div>
                      <div>{{AllStudents$.regiontk}}</div>
                      <div>{{AllStudents$.source_school}}</div>
                    </div>
                    <div class="col-md-2 " style = "font-size: 0.8em" >{{AllStudents$.school_type}}</div>
                    <div class="col-md-3 " style = "font-size: 0.8em" >
                      <div *ngIf="AllStudents$.oldschool !== false"  class= "changecolor" >
                              {{AllStudents$.oldschool}}
                       </div>

                       <div *ngIf="AllStudents$.oldschool !== true" >
                       </div>
                       </div>
                     <div  class="col-md-1"  style="font-size: 0.8em;">
                       <i *ngIf="AllStudents$.oldschool !== false" class="fa fa-undo isclickable" (click)="undosave(AllStudents$.id,2,addressfilter, amfilter)"></i>
                     </div>

       </div>


         <div style="font-weight: bold;">
         <div class="container framecolor">
         <span class="border border-info" >
          <div class="form-group" class="row" style = "font-weight: bold; font-size: 0.8em">
           <div class="col-3">
          Βρίσκεστε στη σελίδα:
          </div>
          <div class="col-1">
           <input #pageno type="text" class="form-control" placeholder=".col-1" formControlName="pageno">
          </div>
          <div class="col-1">
           απο
           </div>
           <div class="col-1">
           <input #maxpage type="text" class="form-control" placeholder=".col-1" formControlName="maxpage" style="width:50px">
           </div>

          <div class="col-4">
           Αριθμός μαθητών ανα σελίδα:
           </div>
          <div class="col-2">
           <select #studentperpage class="form-control"  formControlName="studentperpage" (change)= "changestudentsperpage(studentperpage,0,2)">
                         <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                         <option value="50">50</option>
                        <option value="100">100</option>



            </select>
            </div>
           </div>
             <br>
             <nav aria-label="pagination">
              <ul class="pagination justify-content-center">
                <li class="page-item " >
                  <button class="page-link"  (click)="prevpage(secsel,2)">Προηγούμενη</button>
                </li>
                <li class="page-item">
                  <button class="page-link"  (click) ="nextpage(secsel,2) ">Επόμενη</button>
                </li>
              </ul>

            </nav>
            </span>
         </div>


          <div style="width: 100%; color: #000000; font-weight: bold;" >
                 <p style="margin-top: 20px; line-height: 2em;"> Αφού έχετε επιλέξει τους μαθητες απο την παραπάνω λίστα, στη συνέχεια επιλέξτε το αντίστοιχο Λύκειο υποδοχής.</p>
                 <label> Λύκειο Υποδοχής </label>
                   <select #highscsel2 class="form-control" (change)="confirmSchool(highscsel2,2,addressfilter, amfilter)" >
                        <option value="0"></option>
                        <option *ngFor="let HighSchools$  of HighSchool$ | async; let i=index"
                        [value] = "HighSchools$.id"> {{HighSchools$.name}} </option>
                   </select>
          </div>
                   <br>
                   <br>
        </div>

      </div>


      </div>




 <li class="list-group-item isclickable" (click)="setActiveclass(3)">
          <div *ngIf="hasdone === true" class="col-md-12" style="font-weight: bold;"  [class.selectedout]="aclassActive === 3"
           (click)="setActiveRegion(0,3,1,0,addressfilter, amfilter)"
          > Γ' Λυκείου  </div>
       </li>
      <div [hidden] ="aclassActive !== 3">
           <br>
           <br>
           <div [hidden] ="regionActive === -1">
             <p style="margin-top: 20px; line-height: 2em;"> Σε περίπτωση που θέλετε να εφαρμόσετε φίλτρο στα αποτελέσματα συμπληρώστε το αντιστοιχο πεδίο και πατήστε εφαρμογή</p>
             <div class="col-md-12" style="font-weight: bold;"> Φίλτρα </div>
            <div class="row form-group">
              <div class="col-4">
              <label for="addressfilter">
                Διεύθυνση Κατοικίας:
              </label>
                  <input #addressfilter type= "text" class="form-control" formControlName="addressfilter">
              </div>
              <div class="col-3">
              <label for="amfilter">
                ΑΜ Μαθητή:
              </label>
                  <input #addressfilter type= "text" class="form-control"  formControlName="amfilter">
               </div>
               <div class="col-2">
               <label>
                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              </label>
                <button type="button" class="btn-primary btn-sm  isclickable" style="width: 9em;"  (click)="setActiveRegion(0,3,0,0,addressfilter, amfilter)">
                     Αναζήτηση
                </button>
                </div>
               <div class="col-3">
              <label>
                 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              </label>
                <button type="button" class="btn-primary btn-sm  isclickable" style="width: 9em;" (click)="deletefilters(0,3)" >
                     Καθαρισμός φίλτρου
                </button>

                </div>
           </div>




           <div class="list-group-item framecolor">
                    <div class="col-md-1" style="   font-weight: bold; font-size: 0.8em" >Επιλογή Όλων
                       <input #so type="checkbox" [checked]="selall ===  true" (change)="selectall(3)">
                    </div>
                    <div class="col-md-4" style="   font-weight: bold; font-size: 0.8em" >
                      <div>A/A Αίτησης/ Α.Μ. Μαθητή</div>
                      <div>Διεύθυνση Κατοικίας </div>

                    </div>
                    <div class="col-md-2 " style="   font-weight: bold; font-size: 0.8em" >Τύπος Σχολείου</div>
                    <div class="col-md-3 " style="font-weight: bold;">
                            Σχολείο Τοποθέτησης
                     </div>
                    <div class="col-md-2 ">
                    </div>
          </div>


                 <div *ngFor="let AllStudents$  of StudentsPerSchool$ | async; let l=index; let isOdd=odd; let isEven=even"
                  class="row list-group-item isclickable" [class.oddout]="isOdd" [class.evenout]="isEven"
                   style="margin: 0px 2px 0px 2px;"  [hidden]="calchidden(AllStudents$.idnew)" [class.changecolor]="AllStudents$.oldschool !== false">


                    <div class="col-md-1 " style = "font-size: 0.8em">
                     <input #cb type="checkbox" [checked]="findid(AllStudents$.id)" (change)="updateCheckedOptions(AllStudents$.id, l)">
                   </div>

                    <div class="col-md-4"  style = "font-size: 0.8em">

                      <div>{{AllStudents$.id}}/{{AllStudents$.am}}</div>
                      <div>{{AllStudents$.regionaddress}}</div>
                      <div>{{AllStudents$.regionarea}} </div>
                      <div>{{AllStudents$.regiontk}}</div>
                      <div>{{AllStudents$.source_school}}</div>
                    </div>
                    <div class="col-md-2 " style = "font-size: 0.8em" >{{AllStudents$.school_type}}</div>
                    <div class="col-md-3 " style = "font-size: 0.8em" >
                      <div *ngIf="AllStudents$.oldschool !== false"  class= "changecolor" >
                              {{AllStudents$.oldschool}}
                       </div>

                       <div *ngIf="AllStudents$.oldschool !== true" >
                       </div>
                       </div>
                     <div  class="col-md-1"  style="font-size: 0.8em;">
                       <i *ngIf="AllStudents$.oldschool !== false" class="fa fa-undo isclickable" (click)="undosave(AllStudents$.id,3,addressfilter, amfilter)"></i>
                     </div>

       </div>


         <div style="font-weight: bold;">
         <div class="container framecolor">
         <span class="border border-info" >
          <div class="form-group" class="row" style = "font-weight: bold; font-size: 0.8em">
           <div class="col-3">
          Βρίσκεστε στη σελίδα:
          </div>
          <div class="col-1">
           <input #pageno type="text" class="form-control" placeholder=".col-1" formControlName="pageno">
          </div>
          <div class="col-1">
           απο
           </div>
           <div class="col-1">
           <input #maxpage type="text" class="form-control" placeholder=".col-1" formControlName="maxpage" style="width:50px">
           </div>

          <div class="col-4">
           Αριθμός μαθητών ανα σελίδα:
           </div>
          <div class="col-2">
           <select #studentperpage class="form-control"  formControlName="studentperpage" (change)= "changestudentsperpage(studentperpage,0,3)">
                         <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                         <option value="50">50</option>
                        <option value="100">100</option>



            </select>
            </div>
           </div>
             <br>
             <nav aria-label="pagination">
              <ul class="pagination justify-content-center">
                <li class="page-item " >
                  <button class="page-link"  (click)="prevpage(secsel,3)">Προηγούμενη</button>
                </li>
                <li class="page-item">
                  <button class="page-link"  (click) ="nextpage(secsel,3) ">Επόμενη</button>
                </li>
              </ul>

            </nav>
            </span>
         </div>


          <div style="width: 100%; color: #000000; font-weight: bold;" >
                 <p style="margin-top: 20px; line-height: 2em;"> Αφού έχετε επιλέξει τους μαθητες απο την παραπάνω λίστα, στη συνέχεια επιλέξτε το αντίστοιχο Λύκειο υποδοχής.</p>
                 <label> Λύκειο Υποδοχής </label>
                   <select #highscsel3 class="form-control" (change)="confirmSchool(highscsel3,3,addressfilter, amfilter)" >
                        <option value="0"></option>
                        <option *ngFor="let HighSchools$  of HighSchool$ | async; let i=index"
                        [value] = "HighSchools$.id"> {{HighSchools$.name}} </option>
                   </select>
          </div>
                   <br>
                   <br>
        </div>

      </div>


      </div>

      <li class="list-group-item isclickable" (click)="setActiveclass(40)">
        <div *ngIf="(showLoader | async) === false && hasdone === true" class="col-md-12" style="font-weight: bold;"  [class.selectedout]="aclassActive === 40"
        (click)="getSDEStudents()">ΣΔΕ/ΠΡΟΣΦΥΓΕΣ/ΚΑΤΟΙΚΟΙ ΕΞΩΤΕΡΙΚΟΥ</div>
      </li>
      <div *ngIf="(showLoader | async) === false" [hidden] ="aclassActive !== 40">
        <br>

        <div style="width: 100%; color: #000000; font-weight: bold;" >
        <p style="margin-top: 20px; line-height: 2em;"> Αφού επιλέξετε τους μαθητες απο την παρακάτω λίστα, στη συνέχεια επιλέξτε το αντίστοιχο Λύκειο υποδοχής.</p>
        <input ngui-auto-complete formControlName="lastschool_schoolname" [value] =  " (lastSchName || async).value" [source]="observableSource.bind(this)" [list-formatter]="lastSchoolListFormatter" [value-formatter]="lastSchoolValueFormatter" [min-chars]="5" no-match-found-text="Δεν βρέθηκαν σχολεία"
        (valueChanged)="lastSchoolValueChanged($event)" placeholder="Πληκτρολογήστε τουλάχιστο 5 χαρακτήρες" class="form-control">
        </div>
        <br>
        <br>

        <div *ngIf = "(showLoader | async) === false" class="list-group-item framecolor">

          <div class="col-md-5" style="font-weight: bold; font-size: 1em align:center">
            <div>A/A Αίτησης</div>
            <div>Διεύθυνση Κατοικίας </div>
            <div>T.K., Περιοχή</div>
          </div>
          <div class="col-md-2" style="font-weight: bold; font-size: 1em align:center">Τύπος Σχολείου</div>
          <div class="col-md-4" style="font-weight: bold; font-size: 1em; align:center">Σχολείο Τοποθέτησης</div>
          <div class="col-md-1"></div>
        </div>

        <div *ngIf = "(showLoader | async) === false">
          <div *ngFor="let studentSDE$  of SDEStudents$ | async; let l=index; let isOdd=odd; let isEven=even"
           class="row list-group-item isclickable" [class.oddout]="isOdd" [class.evenout]="isEven"
            style="margin: 0px 2px 0px 2px;">


             <div class="col-md-1 " style = "font-size: 0.8em">
              <input #cb type="checkbox" [checked]="findid(studentSDE$.id)" (change)="updateCheckedOptions(studentSDE$.id, l)">
            </div>

             <div class="col-md-4"  style = "font-size: 0.8em align:left">
               <div>{{studentSDE$.id}}</div>
               <div>{{studentSDE$.regionaddress}}</div>
              <div>{{studentSDE$.regiontk}}{{studentSDE$.regionarea}}</div>
             </div>
             <div class="col-md-2" style = "font-size: 0.8em" ><div>{{studentSDE$.school_type}}</div></div>
             <div class="col-md-4" style = "font-size: 0.8em align:right" >
                <div *ngIf="studentSDE$.oldschool !== false"  class= "changecolor" style = "font-size: 0.8em align:right">
                    {{studentSDE$.oldschool}}
                </div>
                <div *ngIf="studentSDE$.oldschool !== true" style = "font-size: 0.8em align:right">
                </div>
             </div>
             <div  class="col-md-1"  style="font-size: 0.8em;">
                  <i *ngIf="studentSDE$.oldschool !== false" class="fa fa-undo isclickable" (click)="undosaveSDE(studentSDE$.id)"></i>
             </div>
          </div>
        </div>

      </div>



        <li class="list-group-item isclickable" (click)="setActiveclass(300)">
        <div *ngIf="(showLoader | async) === false && hasdone === true" class="col-md-12" style="font-weight: bold;"  [class.selectedout]="aclassActive === 300"
        (click)="getStudentsIdiwtika()">ΜΑΘΗΤΕΣ ΑΠΟ ΙΔΙΩΤΙΚΑ</div>
      </li>
      <div *ngIf="(showLoader | async) === false" [hidden] ="aclassActive !== 300">
        <br>

        <div style="width: 100%; color: #000000; font-weight: bold;" >
        <p style="margin-top: 20px; line-height: 2em;"> Αφού επιλέξετε τους μαθητες απο την παρακάτω λίστα, στη συνέχεια επιλέξτε το αντίστοιχο Λύκειο υποδοχής.</p>
        <input ngui-auto-complete formControlName="lastschool_schoolname_idiwt" [value] =  " (lastSchName || async).value" [source]="observableSource.bind(this)" [list-formatter]="lastSchoolIdiwtListFormatter" [value-formatter]="lastSchoolIdiwtValueFormatter" [min-chars]="5" no-match-found-text="Δεν βρέθηκαν σχολεία"
        (valueChanged)="lastSchoolIdiwtValueChanged($event)" placeholder="Πληκτρολογήστε τουλάχιστο 5 χαρακτήρες" class="form-control">
        </div>
        <br>
        <br>

        <div *ngIf = "(showLoader | async) === false" class="list-group-item framecolor">

          <div class="col-md-1" style="font-weight: bold; font-size: 1em align:center"></div>
          <div class="col-md-4" style="font-weight: bold; font-size: 1em align:center">
            <div>A/A Αίτησης</div>
            <div>Διεύθυνση Κατοικίας </div>
            <div>T.K., Περιοχή</div>
            <div>Σχολείο Προέλευσης</div>
          </div>
          <div class="col-md-2" style="font-weight: bold; font-size: 1em align:center">Τύπος Σχολείου Τοποθέτησης</div>
          <div class="col-md-4" style="font-weight: bold; font-size: 1em; align:center">Σχολείο Τοποθέτησης</div>
          <div class="col-md-1"></div>
        </div>

        <div *ngIf = "(showLoader | async) === false">
          <div *ngFor="let studentIdiwt$  of IDIWTStudents$ | async; let l=index; let isOdd=odd; let isEven=even"
           class="row list-group-item isclickable" [class.oddout]="isOdd" [class.evenout]="isEven"
            style="margin: 0px 2px 0px 2px;">


             <div class="col-md-1 " style = "font-size: 0.8em">
              <input #cb type="checkbox" [checked]="findid(studentIdiwt$.id)" (change)="updateCheckedOptions(studentIdiwt$.id, l)">
            </div>

             <div class="col-md-4"  style = "font-size: 0.8em align:left">
               <div>{{studentIdiwt$.id}}</div>
               <div>{{studentIdiwt$.regionaddress}}</div>
               <div>{{studentIdiwt$.regiontk}}{{studentIdiwt$.regionarea}}</div>
               <div style = "font-size: 0.8em bold; align:left">{{studentIdiwt$.source_school}}</div>
             </div>
             <div class="col-md-2" style = "font-size: 0.8em" ><div>{{studentIdiwt$.school_type}}</div></div>
             <div class="col-md-4" style = "font-size: 0.8em align:right" >
                <div *ngIf="studentIdiwt$.oldschool !== false"  class= "changecolor" style = "font-size: 0.8em align:right">
                    {{studentIdiwt$.oldschool}}
                </div>
                <div *ngIf="studentIdiwt$.oldschool !== true" style = "font-size: 0.8em align:right">
                </div>
             </div>
             <div  class="col-md-1"  style="font-size: 0.8em;">
                  <i *ngIf="studentIdiwt$.oldschool !== false" class="fa fa-undo isclickable" (click)="undosaveIDIWT(studentIdiwt$.id)"></i>
             </div>
          </div>
        </div>


      </div>




      </form>
    </div>




   `
})

@Injectable() export default class GelDistribution implements OnInit, OnDestroy {
    public formGroup: FormGroup;
    private JuniorHighSchool$: BehaviorSubject<any>;
    private JuniorHighSchoolSub: Subscription;
    private HighSchool$: BehaviorSubject<any>;
    private HighSchoolSub: Subscription;
    private StudentsPerSchool$: BehaviorSubject<any>;
    private StudentsPerSchoolSub: Subscription;
    private SaveSelection$: BehaviorSubject<any>;
    private SaveSelectionSub: Subscription;
    private SchoolSelection$: BehaviorSubject<any>;
    private SchoolSelectionSub: Subscription;
    private HighSchoolSelection$: BehaviorSubject<any>;
    private HighSchoolSelectionSub: Subscription;
    private Initialized$: BehaviorSubject<any>;
    private InitializedSub: Subscription;
    private idx = <number>-1;
    private selections: Array<any> = [];
    private showLoader: BehaviorSubject<boolean>;
    private regionActive = <number>-1;
    private modalTitle: BehaviorSubject<string>;
    private modalText: BehaviorSubject<string>;
    private modalHeader: BehaviorSubject<string>;
    private SelectAllIds: Array<any>=[];
    //private SelectAllIdsnew: Array<any>=[];
    private selall:boolean;
    private aclassActive = <number>-1;
    private pageno = 1;
    private pageNew = 1;
    private tot_pages = 1;
    private stperpage = 10;
    private hasdone = false;
    private SDEStudents$: BehaviorSubject<any>;
    private SDEStudentsSub: Subscription;

    private IDIWTStudents$: BehaviorSubject<any>;
    private IDIWTStudentsSub: Subscription;

    private loginInfoSub: Subscription;
    private loginInfo$: BehaviorSubject<ILoginInfoRecords>;
    private lastSchName: BehaviorSubject<string>;

    private sdeLocker: BehaviorSubject<boolean>;
    private privateLocker: BehaviorSubject<boolean>;


    constructor(
        private _hds: HelperDataService,
        private fb: FormBuilder,
        private _ngRedux: NgRedux<IAppState>,
        private http: Http
           ) {
        this.JuniorHighSchool$ = new BehaviorSubject([{}]);
        this.HighSchool$ = new BehaviorSubject([{}]);
        this.StudentsPerSchool$ = new BehaviorSubject([{}]);
        this.SaveSelection$ = new BehaviorSubject([{}]);
        this.SchoolSelection$ = new BehaviorSubject([{}]);
        this.HighSchoolSelection$ = new BehaviorSubject([{}]);
        this.Initialized$ = new BehaviorSubject([{}]);
        this.showLoader = new BehaviorSubject(true);
        this.modalTitle = new BehaviorSubject("");
        this.modalText = new BehaviorSubject("");
        this.modalHeader = new BehaviorSubject("");
        this.formGroup = this.fb.group({
            maxpage:[{value: '', disabled: true}, []],
            pageno:[{value: '', disabled: true}, []],
            secsel:[{value: '', disabled: false}, []],
            studentperpage:[10],
            addressfilter:["",[]],
            amfilter:["",[]],
            lastschool_schoolname: ["", []],
            lastschool_schoolname_idiwt: ["", []],
        });

        this.SDEStudents$= new BehaviorSubject([{}]);
        this.loginInfo$ = new BehaviorSubject(LOGININFO_INITIAL_STATE);
        this.lastSchName = new BehaviorSubject("");
        this.IDIWTStudents$= new BehaviorSubject([{}]);
        this.sdeLocker = new BehaviorSubject(false);
        this.privateLocker = new BehaviorSubject(false);

    }


    ngOnDestroy() {
      (<any>$("#informationfeedback")).remove();

    }

    ngOnInit() {


      this.loginInfoSub = this._ngRedux.select("loginInfo")
      .map(loginInfo => <ILoginInfoRecords>loginInfo)
      .subscribe(linfo => {
        //new piece of code
        if (linfo.size > 0) {
            linfo.reduce(({ }, loginInfoObj) => {
                return loginInfoObj;
            }, {});
          }
          //end new piece of code
          this.loginInfo$.next(linfo);
    }, error => { console.log("error selecting loginInfo"); });


        this.initialized();

       this.selall = false;
       this.selections = [];
        (<any>$("#informationfeedback")).appendTo("body");
       this.getSchools();



    }

     setActiveRegion(ind,type, changed,changepages, addressfilter, amfilter) {

       let addressf = this.formGroup.get('addressfilter').value;
       let amf = this.formGroup.get('amfilter').value;
       this.stperpage = this.formGroup.get('studentperpage').value;

       if (changed === 1)
       {
         this.pageno = 1;
         this.tot_pages = 0;
         this.stperpage = 10;
       }
       if (ind !== 0)
         ind = ind.value;
       this.selall = false;
       this.selections = [];
       this.StudentsPerSchool$.next([{}]);
        if (ind === this.regionActive && ind !== 0 && changed === 1) {
            ind = -1;
           this.regionActive = ind;
          }
        else {
            if (changepages === 1)
            {
              this.pageno = 1
              this.tot_pages = 0;
            }
            this.regionActive = ind;
             this.showLoader.next(true);

           this.formGroup.get('pageno').setValue(this.pageno);


               this.StudentsPerSchoolSub = this._hds.getStudentsPerSchool(this.regionActive,type,addressf, amf)

                .subscribe(data => {

                    this.StudentsPerSchool$.next(data);
                    if (this.pageno == 1){


                    if (data.length < this.stperpage)
                    {

                      this.tot_pages = 1;
                    }
                    else
                    {
                    this.tot_pages = data.length %this.stperpage
                    if (data.length%this.stperpage >0)
                        this.tot_pages = (data.length - (data.length%this.stperpage))/this.stperpage +1;
                    else
                      this.tot_pages = data.length /this.stperpage;
                    }

                    if (this.tot_pages == 0)
                        this.tot_pages = 1;

                    this.formGroup.get('maxpage').setValue(this.tot_pages);

                   }
                   this.pageNew = this.pageno;
                    if (this.selall === true)
                    {

                    this.SelectAllIds = this.StudentsPerSchool$.getValue();
                       for (let i = 0; i < this.SelectAllIds.length; i++) {
                        this.selections[i] = this.SelectAllIds[i].id;
                        }
                      }
                    this.HighSchoolSub = this._hds.getHighSchoolperDide().subscribe(x => {
                        this.HighSchool$.next(x);
                      },
                  error => {
                      this.HighSchool$.next([{}]);
                      console.log("Error Getting Junior High School");
                  });




                    this.showLoader.next(false);
                },
                error => {
                    this.StudentsPerSchool$.next([{}]);
                    console.log("Error Getting Students");

                    this.showLoader.next(false);
                });


  }
}

   confirmSchool( selection,type,addressfilter, amfilter)
   {
       let addressf = this.formGroup.get('addressfilter').value;
       let amf = this.formGroup.get('amfilter').value;

       let oldschool = 0;
       let schoolid = selection.value;
       if (type === 2)
           this.regionActive = 0;
       if (this.selections.length === 0)
       {

           schoolid = 0;
           this.StudentsPerSchoolSub = this._hds.getStudentsPerSchool(this.regionActive,type,addressf, amf)

                .subscribe(data => {
                    this.StudentsPerSchool$.next(data);

                        this.selections = [];
                    this.HighSchoolSub = this._hds.getHighSchoolperDide().subscribe(x => {
                        this.HighSchool$.next(x);


                      },
                  error => {
                      this.HighSchool$.next([{}]);
                      console.log("Error Getting Junior High School");
                  });

                    this.showLoader.next(false);
                },
                error => {
                    this.StudentsPerSchool$.next([{}]);
                    console.log("Error Getting Students");

                    this.showLoader.next(false);
                });
           this.modalHeader.next("modal-header-danger");
           this.modalTitle.next("Δεν επιλέξατε μαθητές.");
           this.modalText.next("Επιλέξτε μαθητές και στη συνέχεια το Λύκειο υποδοχής τους. ");
           this.showModal();
           this.selall = false;
           this.selections = [];


       }
        else{


        this.SaveSelectionSub = this._hds.saveHighScoolSelection(this.selections, oldschool, schoolid,type, 0).subscribe(data => {
            this.SaveSelection$.next(data);
            this.showLoader.next(false);
            this.selections = [];
            this.selall = false;
            this.modalHeader.next("modal-header-success");
            this.modalTitle.next("Αποθηκεύτηκαν.");
            this.modalText.next("Οι επιλογές σας έχουν αποθηκευτεί.");
           this.showModal();
            this.StudentsPerSchoolSub = this._hds.getStudentsPerSchool(this.regionActive,type,addressf, amf)

                .subscribe(data => {
                    this.StudentsPerSchool$.next(data);
                    this.selections = [];
                    this.HighSchoolSub = this._hds.getHighSchoolperDide().subscribe(x => {
                        this.HighSchool$.next(x);


                      },
                  error => {
                      this.HighSchool$.next([{}]);
                      console.log("Error Getting Junior High School");
                  });




                    this.showLoader.next(false);
                },
                error => {
                    this.StudentsPerSchool$.next([{}]);
                    console.log("Error Getting Students");

                    this.showLoader.next(false);
                });




          },
            error => {
                this.SaveSelection$.next([{}]);
                console.log("Error saving Approved");
                this.showLoader.next(false);
               });
      }

   }

  findselection(ind)
  {

                  this.HighSchoolSelectionSub = this._hds.getHighSchoolSelection(ind).subscribe(x => {
                  this.HighSchoolSelection$.next(x);

                  },
                  error => {
                      this.HighSchoolSelection$.next([{}]);
                      console.log("No HighSchool");
                  });
  }

updateCheckedOptions(k,l)
{

  let server = 0;
  server = this.selections.find(x => x === k);
  let index: number = this.selections.indexOf(server);
 if (index === -1 )
 {
  this.selections.push(k);

 }
 else
 {
       this.selections.splice(index, 1);

 }

 }



findid(id)
{
let server = 0;
  server = this.selections.find(x => x === id);
  let index: number = this.selections.indexOf(server);
  if (index !== -1 && this.selall === true)
  {

    return true;

  }

  return false;


  /*for (let i = 0; i < this.SelectAllIdsnew.length; i++)
  {
    if (id === this.SelectAllIdsnew[i] && this.selall === true){
        console.log(this.selall);
        this.updateCheckedOptions(this.SelectAllIdsnew[i], id)
        return true;
      }

  }
  return false;*/
}

selectall(type,addressfilter, amfilter)
{
       let addressf = this.formGroup.get('addressfilter').value;
       let amf = this.formGroup.get('amfilter').value;


          this.selall =! this.selall;
           this.showLoader.next(true);
            this.StudentsPerSchoolSub = this._hds.getStudentsPerSchool(this.regionActive,type,addressf, amf)

                .subscribe(data => {
                    this.StudentsPerSchool$.next(data);
                    if (this.selall === true)
                    {


                    this.SelectAllIds = this.StudentsPerSchool$.getValue();
                       for (let i = 0; i < this.SelectAllIds.length; i++) {
                         if (this.SelectAllIds[i].idnew >= (this.pageNew-1)*this.stperpage + 1 && this.SelectAllIds[i].idnew <= this.pageNew*this.stperpage)
                            this.selections[i] = this.SelectAllIds[i].id;
                        }
                      }
                      else
                      {
                           this.selections = [];
                      }
                    this.HighSchoolSub = this._hds.getHighSchoolperDide().subscribe(x => {
                        this.HighSchool$.next(x);
                      },
                  error => {
                      this.HighSchool$.next([{}]);
                      console.log("Error Getting Junior High School");
                  });




                    this.showLoader.next(false);
                },
                error => {
                    this.StudentsPerSchool$.next([{}]);
                    console.log("Error Getting Students");

                    this.showLoader.next(false);
                });

}




 setActiveclass(ind)
    {

        this.initialized();
       if (this.hasdone === false)
        {
                this.modalHeader.next("modal-header-danger");
                 this.modalTitle.next("Απαιτείται Αρχικοποίηση");
                 this.modalText.next("Παρακαλώ αρχικοποιήστε τους μαθητές σας.");
                 this.showModal();
        }
        else
        {
      if (this.aclassActive === ind)
      {
             ind = -1;
             this.regionActive = -1;
             this.formGroup.get('secsel').setValue(0);
             this.stperpage = 10;


      }
      if (ind == 1)
                this.formGroup.get('secsel').setValue("0");
      this.aclassActive = ind;
    }
      }


    public showModal(): void {
        (<any>$("#informationfeedback")).modal("show");
    }

    public hideModal(): void {
        (<any>$("#informationfeedback")).modal("hide");
    }



    calchidden(idn)
    {

         if (idn < (this.pageNew-1)*this.stperpage + 1 || idn > this.pageNew*this.stperpage)
            return true;
         else
             return false;

    }

    nextpage(secsel,type)
    {
      if (this.pageno === this.tot_pages)
       return;

        this.pageno = this.pageno + 1;
       this.setActiveRegion(secsel,type,0,0,"","")

    }


    prevpage(secsel,type)
    {
      if (this.pageno ===1)
        return;
      this.pageno = this.pageno - 1;
      this.setActiveRegion(secsel,type,0,0,"","")

    }


changestudentsperpage(newstperpage,secsel,type)
{

  this.stperpage= newstperpage.value ;
  this.setActiveRegion(secsel,1,0,type,"","");

}

initialaizattion()
{
if (this.hasdone === false)
 {
   this.showLoader.next(true);
this._hds.Initialazation()
            .then(msg => {
              this.showLoader.next(false);
                 this.hasdone = true;
                 this.showLoader.next(false);
                 this.modalHeader.next("modal-header-success");
                 this.modalTitle.next("Αρχικοποίηση");
                 this.modalText.next("Έχετε αρχικοποιήσει σωστά τους μαθητές σας.");
                 this.showModal();
                 //console.log(this.hasdone,"initialzation")

            })
            .catch(err => {
                console.log(err);

            });

 }
 else
 {
           this.modalHeader.next("modal-header-danger");
           this.modalTitle.next("Αποτυχία Αρχικοποίησης");
           this.modalText.next("Έχετε ήδη αρχικοποιήσει τους μαθητές σας!");
           this.showModal();
 }

}

undosave(nid,type, addressfilter, amfilter)
{
    let addressf = this.formGroup.get('addressfilter').value;
    let amf = this.formGroup.get('amfilter').value;
    if (type == 2)
       this.regionActive = 0;
   this.SaveSelectionSub = this._hds.saveHighScoolSelection(nid, 0, 0,1, 1).subscribe(data => {
            this.SaveSelection$.next(data);
            this.showLoader.next(false);
            this.selections = [];
            this.selall = false;
            this.modalHeader.next("modal-header-success");
            this.modalTitle.next("Έγινε αναίρεση τοποθέτησης .");
            this.modalText.next("Οι επιλογές σας έχουν αποθηκευτεί.");
            this.showModal();
            this.StudentsPerSchoolSub = this._hds.getStudentsPerSchool(this.regionActive,type,addressf, amf)

                .subscribe(data => {
                    this.StudentsPerSchool$.next(data);
                    this.selections = [];
                    this.HighSchoolSub = this._hds.getHighSchoolperDide().subscribe(x => {
                        this.HighSchool$.next(x);


                      },
                  error => {
                      this.HighSchool$.next([{}]);
                      console.log("Error Getting Junior High School");
                  });




                    this.showLoader.next(false);
                },
                error => {
                    this.StudentsPerSchool$.next([{}]);
                    console.log("Error Undo");

                    this.showLoader.next(false);
                });


});
}


deletefilters(secsel,classid)
{

  this.formGroup.get('addressfilter').setValue("");
  this.formGroup.get('amfilter').setValue("");
  this.setActiveRegion(secsel,classid,0,0,"", "")
}


initialized()
{

      //this.hasdone = true;
      //this.showLoader.next(false);


       this.InitializedSub = this._hds.findIfInitialized()

                .subscribe(data => {
                    this.Initialized$.next(data);
                    if (data.answer === true)
                    {
                      this.hasdone = true;
                      this.showLoader.next(false);

                    }
                    else
                    {
                      this.hasdone = false;
                      this.showLoader.next(false);

                    }

                },
                error => {
                    this.Initialized$.next([{}]);

                });

 }

getSchools()
{
 this.JuniorHighSchoolSub = this._hds.getJuniorHighSchoolperDide().subscribe(x => {
            this.JuniorHighSchool$.next(x);

        },
            error => {
                this.JuniorHighSchool$.next([{}]);
                console.log("Error Getting Junior High School");
            });
}

getSDEStudents() {

    if (this.sdeLocker.getValue()) {
      this.sdeLocker.next(!this.sdeLocker.getValue());
      return;
    }

    this.showLoader.next(true);

    this.SDEStudentsSub = this._hds.getAllSDEStudents().subscribe(data => {
      this.SDEStudents$.next(data);
      this.showLoader.next(false);
    },
    error => {
      this.StudentsPerSchool$.next([{}]);
      console.log("Error Getting SDE Students");
      this.showLoader.next(false);
    });

    this.sdeLocker.next(true);

}

private observableSource = (keyword: any): Observable<any[]> => {

  let headers = new Headers({
      "Content-Type": "application/json",
  });
  this.loginInfo$.getValue().forEach(loginInfoToken => {
      headers.append("Authorization", "Basic " + btoa( loginInfoToken.auth_token + ":" +  loginInfoToken.auth_token));
 });


 let options = new RequestOptions({ headers: headers });

 let url: string = `${AppSettings.API_ENDPOINT}/deploysystem/getgelschoollist/` + keyword ;

  if (keyword) {
      return this.http.get(url, options)
          .map(res => {
              let json = res.json();
              let retArr = <any>Array();
              //for (let i = 0; i < json.data.length; i++) {
              for (let i = 0; i < json.length; i++) {
                  retArr[i] = {};
                  //retArr[i].registry_no = json.data[i].registry_no;
                  //retArr[i].name = json.data[i].name;
                  //retArr[i].id = json.data[i].id;
                  retArr[i].registry_no = json[i].registry_no;
                  retArr[i].name = json[i].name;
                  retArr[i].unit_type_id = json[i].unit_type_id;
                  retArr[i].school_id = json[i].school_id;

              }
              return retArr;
          });
  } else {
      return Observable.of([]);
  }
};

lastSchoolListFormatter(data: any): string {
  return data.name;
};

lastSchoolValueFormatter(data: any): string {
  return data.name;
};

lastSchoolValueChanged(e: any): void {
  this.confirmSDE();
};

lastSchoolIdiwtListFormatter(data: any): string {
  return data.name;
};

lastSchoolIdiwtValueFormatter(data: any): string {
  return data.name;
};

lastSchoolIdiwtValueChanged(e: any): void {
  this.confirmIDIWT();
};

undosaveSDE(nid)
{

   this.showLoader.next(true);

  this.SaveSelectionSub = this._hds.saveHighScoolSelectionforSDE(nid, 0,1).subscribe(data => {

            this.SaveSelection$.next(data);

            this.SDEStudentsSub = this._hds.getAllSDEStudents().subscribe(data => {

              this.SDEStudents$.next(data);
            },
            error => {
              this.StudentsPerSchool$.next([{}]);
              console.log("Error Getting SDE Students");
              this.showLoader.next(false);
            });

    },
        error => {
          this.StudentsPerSchool$.next([{}]);
          console.log("Error Undo");
          this.showLoader.next(false);
        });

    this.showLoader.next(false);
    this.modalHeader.next("modal-header-success");
    this.modalTitle.next("Έγινε αναίρεση τοποθέτησης .");
    this.modalText.next("Οι επιλογές σας έχουν αποθηκευτεί.");
    this.showModal();
    this.selall = false;
    this.selections = [];


}

confirmSDE()
{


    let oldschool = 0;
    let schoolid = this.formGroup.controls["lastschool_schoolname"].value.school_id;
    //console.log(schoolid);

    if (this.selections.length === 0)
    {

        schoolid = 0;
        this.modalHeader.next("modal-header-danger");
        this.modalTitle.next("Δεν επιλέξατε μαθητές.");
        this.modalText.next("Επιλέξτε μαθητές και στη συνέχεια το Λύκειο υποδοχής τους. ");
        this.showModal();
        this.selall = false;
        this.selections = [];
        //console.log('sde')
        this.formGroup.controls["lastschool_schoolname"].setValue("");
        this.formGroup.controls["lastschool_schoolname"].updateValueAndValidity();



    }
     else{

      this.showLoader.next(true);


      this.SaveSelectionSub = this._hds.saveHighScoolSelectionforSDE(this.selections, schoolid,0).subscribe(data => {
         this.SaveSelection$.next(data);

         this.SDEStudentsSub = this._hds.getAllSDEStudents().subscribe(data => {
             this.SDEStudents$.next(data);
          },
          error => {
            this.StudentsPerSchool$.next([{}]);
            console.log("Error Getting SDE Students");
            this.showLoader.next(false);
          });
          schoolid = 0;


       },
        error => {
          this.SaveSelection$.next([{}]);
          console.log("Error saving Approved");
          this.showLoader.next(false);
        });


        this.showLoader.next(false);
        this.modalHeader.next("modal-header-success");
        this.modalTitle.next("Αποθηκεύτηκαν.");
        this.modalText.next("Οι επιλογές σας έχουν αποθηκευτεί.");
        this.showModal();
        this.selall = false;
        this.selections = [];
        this.formGroup.controls["lastschool_schoolname"].setValue("");
        this.formGroup.controls["lastschool_schoolname"].updateValueAndValidity();

   }

}


getStudentsIdiwtika() {

  if (this.privateLocker.getValue()) {
    this.privateLocker.next(!this.privateLocker.getValue());
    return;
  }

  this.showLoader.next(true);

  this.IDIWTStudentsSub = this._hds.getAllIDIWTStudents().subscribe(data => {
    this.IDIWTStudents$.next(data);
    this.showLoader.next(false);
  },

  error => {
    this.StudentsPerSchool$.next([{}]);
    console.log("Error Getting SDE Students");
    this.showLoader.next(false);
  });

  this.privateLocker.next(true);


}



undosaveIDIWT(nid)
{

   this.showLoader.next(true);

  this.SaveSelectionSub = this._hds.saveHighScoolSelectionforIDIWT(nid, 0,1).subscribe(data => {

            this.SaveSelection$.next(data);

            this.IDIWTStudentsSub = this._hds.getAllIDIWTStudents().subscribe(data => {

              this.IDIWTStudents$.next(data);
            },
            error => {
              this.StudentsPerSchool$.next([{}]);
              console.log("Error Getting SDE Students");
              this.showLoader.next(false);
            });

    },
        error => {
          this.StudentsPerSchool$.next([{}]);
          console.log("Error Undo");
          this.showLoader.next(false);
        });

    this.showLoader.next(false);
    this.modalHeader.next("modal-header-success");
    this.modalTitle.next("Έγινε αναίρεση τοποθέτησης .");
    this.modalText.next("Οι επιλογές σας έχουν αποθηκευτεί.");
    this.showModal();
    this.selall = false;
    this.selections = [];


}

confirmIDIWT()
{


    let oldschool = 0;
    let schoolid = this.formGroup.controls["lastschool_schoolname_idiwt"].value.school_id;
    //console.log(schoolid);

    if (this.selections.length === 0)
    {

        schoolid = 0;
        this.modalHeader.next("modal-header-danger");
        this.modalTitle.next("Δεν επιλέξατε μαθητές.");
        this.modalText.next("Επιλέξτε μαθητές και στη συνέχεια το Λύκειο υποδοχής τους. ");
        this.showModal();
        this.selall = false;
        this.selections = [];
        //console.log('sde')
        this.formGroup.controls["lastschool_schoolname_idiwt"].setValue("");
        this.formGroup.controls["lastschool_schoolname_idiwt"].updateValueAndValidity();



    }
     else{

      this.showLoader.next(true);


      this.SaveSelectionSub = this._hds.saveHighScoolSelectionforIDIWT(this.selections, schoolid,0).subscribe(data => {
         this.SaveSelection$.next(data);

         this.IDIWTStudentsSub = this._hds.getAllIDIWTStudents().subscribe(data => {
             this.IDIWTStudents$.next(data);
          },
          error => {
            this.StudentsPerSchool$.next([{}]);
            console.log("Error Getting SDE Students");
            this.showLoader.next(false);
          });
          schoolid = 0;


       },
        error => {
          this.SaveSelection$.next([{}]);
          console.log("Error saving Approved");
          this.showLoader.next(false);
        });


        this.showLoader.next(false);
        this.modalHeader.next("modal-header-success");
        this.modalTitle.next("Αποθηκεύτηκαν.");
        this.modalText.next("Οι επιλογές σας έχουν αποθηκευτεί.");
        this.showModal();
        this.selall = false;
        this.selections = [];
        this.formGroup.controls["lastschool_schoolname_idiwt"].setValue("");
        this.formGroup.controls["lastschool_schoolname_idiwt"].updateValueAndValidity();

   }

}


}
