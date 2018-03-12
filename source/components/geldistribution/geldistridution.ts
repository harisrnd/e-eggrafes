import { Component, OnDestroy, OnInit } from "@angular/core";
import { Injectable } from "@angular/core";
import { BehaviorSubject, Subscription } from "rxjs/Rx";

import { HelperDataService } from "../../services/helper-data-service";

@Component({
    selector: "gel-distribution",
    template: `
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
                 <div *ngFor="let AllStudents$  of StudentsPerSchool$ | async; let l=index; let isOdd=odd; let isEven=even"
                  class="row list-group-item isclickable" [class.oddout]="isOdd" [class.evenout]="isEven" style="margin: 0px 2px 0px 2px;">
                    <div class="col-md-2" style="   font-weight: bold;" >{{AllStudents$.id}}</div>
                    <div class="col-md-10" style="   font-weight: bold;" >{{AllStudents$.regionaddress}}</div>
                    <div class="col-md-6 offset-md-2" style="   font-weight: bold;" >{{AllStudents$.regionarea}}</div>
                    <div class="col-md-4 offset-md-2" style="   font-weight: bold;" >{{AllStudents$.regiontk}}</div>
                    <div *ngIf="AllStudents$.oldschool !== false" class="col-md-10 offset-md-2" style="   font-weight: bold;" >{{AllStudents$.oldschool}}</div>
                    
                    <i *ngIf="!isEdit" (click)= "confirmSchool(AllStudents$.id,AllStudents$.oldschool, 0)" class="fa fa-pencil isclickable pull-right" style="font-size: 1.5em;"></i>
                    
                    <div  *ngIf="AllStudents$.oldschool === false" class="col-md-11 offset-md-1">
                    <label> Λύκειο Υποδοχής </label>
                    <select #highscsel class="form-control" (change)="confirmSchool(AllStudents$.id,AllStudents$.oldschool, highscsel)" >
                        <option value="0"></option>
                        <option *ngFor="let HighSchools$  of HighSchool$ | async; let i=index" 
                        [value] = "HighSchools$.id"> {{HighSchools$.name}} </option>
                    </select>
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

   
    private showLoader: BehaviorSubject<boolean>;
    private regionActive = <number>-1;

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
       
    }

    
    ngOnDestroy() {

    }

    ngOnInit() {
     
       

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

   confirmSchool( studentid, oldschool, selection)
   {

       let schoolid = selection.value;
       if (selection === 0)
           schoolid = 0;
        console.log(schoolid, "tralalala")
        this.SaveSelectionSub = this._hds.saveHighScoolSelection(studentid,oldschool, schoolid).subscribe(data => {
            this.SaveSelection$.next(data);
            this.showLoader.next(false);
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


          },
            error => {
                this.SaveSelection$.next([{}]);
                console.log("Error saving Approved");
                this.showLoader.next(false);
               });

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

}
