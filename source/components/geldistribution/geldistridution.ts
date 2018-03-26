import { Component, OnDestroy, OnInit } from "@angular/core";
import { Injectable } from "@angular/core";
import { BehaviorSubject, Subscription } from "rxjs/Rx";

import { HelperDataService } from "../../services/helper-data-service";

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
    <form>


       <p style="margin-top: 20px; line-height: 2em;"> Στην παρακάτω λίστα βλέπετε τα γυμνάσια της περιοχής ευθύνης σας. Παρακαλώ
       επιλέξτε σχολειο για να τοποθετήσετε τους μαθητές με βάση τη διεύθυνση κατοικίας τους στο αντίστοιχο λύκειο.</p>
      <div class="row" style="margin-top: 20px; line-height: 2em;" > <b> Τα Γυμνάσια ευθύνης σας. </b></div>
      <div *ngFor="let JuniorHighSchools$  of JuniorHighSchool$ | async; let i=index; let isOdd=odd; let isEven=even" >
                <li class="list-group-item " [class.oddout]="isOdd" [class.evenout]="isEven" 
                (click)="setActiveRegion(JuniorHighSchools$.id)" [class.selectedout]="regionActive === JuniorHighSchools$.id" >
                <div class="row">
                <div class="col-md-12">
                   <h5>{{JuniorHighSchools$.name}}</h5>
                 </div>
                 </div>

                 </li>
                 <div [hidden]="regionActive !== JuniorHighSchools$.id" >
                 <p style="margin-top: 20px; line-height: 2em;"> Παρακαλώ επιλέξτε τους μαθητές που θέλετε να τοποθετήσετε σε κάποιο Λύκειο
                 και στη συνέχεια επιλέξτε το αντίστοιχο Λυκειο.</p>
                 <label> Λύκειο Υποδοχής </label>
                   <select #highscsel class="form-control" (change)="confirmSchool(highscsel)" >
                        <option value="0"></option>
                        <option *ngFor="let HighSchools$  of HighSchool$ | async; let i=index" 
                        [value] = "HighSchools$.id"> {{HighSchools$.name}} </option>
                   </select>
                   <br>
                   <br>
                  <div class = "row selectedout" [hidden]="regionActive !== JuniorHighSchools$.id" style="margin: 0px 2px 0px 2px;">
                     <div class="col-md-1">
                                                    
                     </div>
                    <div class="col-md-1" style="   font-weight: bold;" >A/A Αίτησης</div>
                    <div class="col-md-2" style="   font-weight: bold;" >ΑΜ Μαθητη</div>
                    
                    <div class="col-md-4" style="   font-weight: bold;" >Διεύθυνση</div>
                    <div class="col-md-3 " style="   font-weight: bold;" >Περιοχή</div>
                    <div class="col-md-1 " style="   font-weight: bold;" >ΤΚ</div>
                   </div>
                 <div *ngFor="let AllStudents$  of StudentsPerSchool$ | async; let l=index; let isOdd=odd; let isEven=even"
                  class="row list-group-item isclickable" [class.oddout]="isOdd" [class.evenout]="isEven" style="margin: 0px 2px 0px 2px;">
                    <div class="col-md-1 ">
                     <input #cb type="checkbox" (change)="updateCheckedOptions(AllStudents$.id, l)">                               
                   </div>
                    <div class="col-md-1" style="   font-weight: bold;" >{{AllStudents$.id}}</div>
                    <div class="col-md-2" style="   font-weight: bold;" >{{AllStudents$.am}}</div>
                    
                    <div class="col-md-4" style="   font-weight: bold;" >{{AllStudents$.regionaddress}}</div>
                    <div class="col-md-3 " style="   font-weight: bold;" >{{AllStudents$.regionarea}}</div>
                    <div class="col-md-1 " style="   font-weight: bold;" >{{AllStudents$.regiontk}}</div>
                    <div *ngIf="AllStudents$.oldschool !== false" class="col-md-10 offset-md-2" style="   font-weight: bold;" >{{AllStudents$.oldschool}}</div>
                    
                   
                    
                    <div  *ngIf="AllStudents$.oldschool === false" class="col-md-11 offset-md-1">
                    
                    </div>
                 </div>
                 </div>


              
       </div>

      </form>
    </div>

  

   `
})

@Injectable() export default class GelDistribution implements OnInit, OnDestroy {

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
    private idx = <number>-1;
    private selections: Array<any> = [];
    private showLoader: BehaviorSubject<boolean>;
    private regionActive = <number>-1;
    private modalTitle: BehaviorSubject<string>;
    private modalText: BehaviorSubject<string>;
    private modalHeader: BehaviorSubject<string>;




    constructor(
        private _hds: HelperDataService,
           ) {
        this.JuniorHighSchool$ = new BehaviorSubject([{}]);
        this.HighSchool$ = new BehaviorSubject([{}]);
        this.StudentsPerSchool$ = new BehaviorSubject([{}]);
        this.SaveSelection$ = new BehaviorSubject([{}]);
        this.SchoolSelection$ = new BehaviorSubject([{}]);
        this.HighSchoolSelection$ = new BehaviorSubject([{}]);
        this.showLoader = new BehaviorSubject(false);
        this.modalTitle = new BehaviorSubject("");
        this.modalText = new BehaviorSubject("");
        this.modalHeader = new BehaviorSubject("");
       
    }

    
    ngOnDestroy() {
      (<any>$("#informationfeedback")).remove();

    }

    ngOnInit() {
     
        (<any>$("#informationfeedback")).appendTo("body");
        this.JuniorHighSchoolSub = this._hds.getJuniorHighSchoolperDide().subscribe(x => {
            this.JuniorHighSchool$.next(x);

        },
            error => {
                this.JuniorHighSchool$.next([{}]);
                console.log("Error Getting Junior High School");
            });
    }

     setActiveRegion(ind) {
        this.StudentsPerSchool$.next([{}]);
        if (ind === this.regionActive) {
            ind = -1;
            this.regionActive = ind;
        }
        else {
            this.regionActive = ind;
            this.showLoader.next(true);
            this.StudentsPerSchoolSub = this._hds.getStudentsPerSchool(this.regionActive)

                .subscribe(data => {
                    this.StudentsPerSchool$.next(data);
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

   confirmSchool(  selection)
   {

       let oldschool = 0;
       let schoolid = selection.value;
       if (this.selections.length === 0)
       {
           schoolid = 0;
           this.StudentsPerSchoolSub = this._hds.getStudentsPerSchool(this.regionActive)

                .subscribe(data => {
                    this.StudentsPerSchool$.next(data);
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

           
       }
        else{
       
        this.SaveSelectionSub = this._hds.saveHighScoolSelection(this.selections, oldschool, schoolid).subscribe(data => {
            this.SaveSelection$.next(data);
            this.showLoader.next(false);
            this.selections = [];
            this.StudentsPerSchoolSub = this._hds.getStudentsPerSchool(this.regionActive)

                .subscribe(data => {
                    this.StudentsPerSchool$.next(data);
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
               this.modalHeader.next("modal-header-success");
               this.modalTitle.next("Αποθηκεύτηκαν.");
               this.modalText.next("Οι επιλογές σας έχουν αποθηκευτεί.");



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
 if (index === -1)
 {
  this.selections.push(k);
 }
 else
 {
    this.selections.splice(index, 1);   
 }  
  
}


    public showModal(): void {
        (<any>$("#informationfeedback")).modal("show");
    }

    public hideModal(): void {
        (<any>$("#informationfeedback")).modal("hide");
    }

}
