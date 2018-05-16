import { Component, OnDestroy, OnInit } from "@angular/core";
import { Injectable } from "@angular/core";
import { BehaviorSubject, Subscription } from "rxjs/Rx";
import { HelperDataService } from "../../services/helper-data-service";
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


       <p style="margin-top: 20px; line-height: 2em;"> Στην παρακάτω λίστα βλέπετε τα γυμνάσια της περιοχής ευθύνης σας. Παρακαλώ
       επιλέξτε σχολειο για να τοποθετήσετε τους μαθητές με βάση τη διεύθυνση κατοικίας τους στο αντίστοιχο λύκειο.</p>
       <div style="font-weight: bold;">
       <li class="list-group-item isclickable" (click)="setActiveclass(1)">
          <div class="col-md-12"  [class.selectedout]="aclassActive === 1" > Ά Λυκείου  </div>
       </li>    
      <div [hidden] ="aclassActive !== 1">   
      <div class="row" style="margin-top: 20px; line-height: 2em;" > <b> Επιλέξτε Γυμνάσιο Προέλευσης</b></div>
            <div class="col-md-11 offset-md-1">
                
                <select #secsel class="form-control"  
                        (change)="setActiveRegion(secsel,1,1)">
                    <option value="0"></option>
                    <option *ngFor="let JuniorHighSchools$  of JuniorHighSchool$ | async; let i=index; let isOdd=odd; let isEven=even" [value]="JuniorHighSchools$.id"> {{JuniorHighSchools$.name}}</option>
                </select>
                </div>
           <br>
           <br>
           <div class="list-group-item selectedout">
                    <div class="col-md-2" style="   font-weight: bold;" >Επιλογή Όλων 
                       <input #so type="checkbox" [checked]="selall ===  true" (change)="selectall()">                               
                    </div>
                    <div class="col-md-4" style="   font-weight: bold;" >
                      <div>A/A Αίτησης/ Α.Μ. Μαθητή</div> 
                      <div>Διεύθυνση Κατοικίας </div>
                      
                    </div>
                    <div class="col-md-2 " style="   font-weight: bold;" >Τύπος Σχολείου</div>
                    <div class="col-md-4 " style="font-weight: bold;">
                            Σχολείο Τοποθέτησης
                     </div>

                     </div>
                 
                 
                 <div *ngFor="let AllStudents$  of StudentsPerSchool$ | async; let l=index; let isOdd=odd; let isEven=even"
                  class="row list-group-item isclickable" [class.oddout]="isOdd" [class.evenout]="isEven"
                   style="margin: 0px 2px 0px 2px;"  [hidden]="calchidden(AllStudents$.idnew)" [class.changecolor]="AllStudents$.oldschool !== false">

                    
                    <div class="col-md-2 " >
                     <input #cb type="checkbox" [checked]="findid(AllStudents$.id)" (change)="updateCheckedOptions(AllStudents$.id, l)">                               
                   </div>

                    <div class="col-md-4" style="   font-weight: bold;" >

                      <div>{{AllStudents$.id}}/{{AllStudents$.am}}</div> 
                      <div>{{AllStudents$.regionaddress}}</div>
                      <div>{{AllStudents$.regionarea}} </div>
                      <div>{{AllStudents$.regiontk}}</div>
                    </div>
                    <div class="col-md-2 " style="   font-weight: bold;" >{{AllStudents$.school_type}}</div>
                    <div *ngIf="AllStudents$.oldschool !== false" class="col-md-4 changecolor" style="font-weight: bold;">
                            {{AllStudents$.oldschool}}
                     </div>

                     <div *ngIf="AllStudents$.oldschool !== true" class="col-md-4" style="font-weight: bold;">
                           
                     </div>
                                     
              
                 </div>
 
     

                
              
         <div class="container selectedout">
         <span class="border border-info">
          <div class="form-group" class="row">
         
          Βρίσκεστε στη σελίδα:
          <div class="col-1">
           <input #pageno type="text" class="form-control" placeholder=".col-1" formControlName="pageno">
          </div> 
           απο  
           <div class="col-1">
           <input #maxpage type="text" class="form-control" placeholder=".col-1" formControlName="maxpage">
           </div> 
           </div>
 
           
             <br>
             <nav aria-label="pagination">
              <ul class="pagination justify-content-center">
                <li class="page-item " >
                  <button class="page-link"  (click)="prevpage(secsel)">Προηγούμενη</button>
                </li>
                <li class="page-item">
                  <button class="page-link"  (click) ="nextpage(secsel) ">Επόμενη</button>
                </li>
              </ul>
              
            </nav>
            </span>
         </div>


          <div style="width: 100%; color: #000000;">
                 <p style="margin-top: 20px; line-height: 2em;"> Αφού έχετε επιλέξει τους μαθητες απο την παραπάνω λίστα, στη συνέχεια επιλέξτε το αντίστοιχο Λύκειο υποδοχής.</p>
                 <label> Λύκειο Υποδοχής </label>
                   <select #highscsel class="form-control" (change)="confirmSchool(highscsel,1)" >
                        <option value="0"></option>
                        <option *ngFor="let HighSchools$  of HighSchool$ | async; let i=index" 
                        [value] = "HighSchools$.id"> {{HighSchools$.name}} </option>
                   </select>
          </div>
                   <br>
                   <br>
        

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




    constructor(
        private _hds: HelperDataService,
        private fb: FormBuilder,
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
        this.formGroup = this.fb.group({
            maxpage:[{value: '', disabled: true}, []],
            pageno:[{value: '', disabled: true}, []],
        });

       
    }

    
    ngOnDestroy() {
      (<any>$("#informationfeedback")).remove();

    }

    ngOnInit() {
      console.log(this.aclassActive,"aaaaaaa" );
       this.selall = false;
       this.selections = [];
       console.log(this.selall);
        (<any>$("#informationfeedback")).appendTo("body");
        this.JuniorHighSchoolSub = this._hds.getJuniorHighSchoolperDide().subscribe(x => {
            this.JuniorHighSchool$.next(x);

        },
            error => {
                this.JuniorHighSchool$.next([{}]);
                console.log("Error Getting Junior High School");
            });
    }

     setActiveRegion(ind,type, changed) {
       
       if (changed === 1)
       {
         this.pageno = 1;
         this.tot_pages = 0;
       }


       ind = ind.value;
       this.selall = false;
       this.selections = [];
        console.log(this.selall);
        this.StudentsPerSchool$.next([{}]);
        if (ind === this.regionActive && ind !== 0 && changed === 1) {
            ind = -1;
            this.regionActive = ind;
        }
        else {

            this.regionActive = ind;
            this.showLoader.next(true);

           this.formGroup.get('pageno').setValue(this.pageno); 
      
              
               this.StudentsPerSchoolSub = this._hds.getStudentsPerSchool(this.regionActive,type)

                .subscribe(data => {
                    console.log("first", this.pageno);
                    this.StudentsPerSchool$.next(data);
                    if (this.pageno == 1){
                    this.tot_pages = data.length %3;
                    if (data.length%3 >0)
                        this.tot_pages = (data.length - (data.length%3))/3 +1;
                    else
                      this.tot_pages = data.length /3;
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

   confirmSchool( selection,type)
   {
       let oldschool = 0;
       let schoolid = selection.value;
       console.log(selection.value, type, "tralala");
       if (this.selections.length === 0)
       {

           schoolid = 0;
           this.StudentsPerSchoolSub = this._hds.getStudentsPerSchool(this.regionActive,type)

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
         console.log(this.selections, oldschool, "aaaaa",schoolid ,"tralala1");

        this.SaveSelectionSub = this._hds.saveHighScoolSelection(this.selections, oldschool, schoolid,type).subscribe(data => {
            this.SaveSelection$.next(data);
            this.showLoader.next(false);
            this.selections = [];
            this.selall = false;
            this.modalHeader.next("modal-header-success");
            this.modalTitle.next("Αποθηκεύτηκαν.");
            this.modalText.next("Οι επιλογές σας έχουν αποθηκευτεί.");
            this.StudentsPerSchoolSub = this._hds.getStudentsPerSchool(this.regionActive,type)

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
                  console.log(this.aclassActive,"aaaaaaa2" );
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
  console.log(this.aclassActive,"aaaaaaa3" );
  let server = 0;
  server = this.selections.find(x => x === k);
  let index: number = this.selections.indexOf(server);
 if (index === -1 )
 {
  this.selections.push(k);
  console.log(this.selections,"selections")
 }
 else
 {
       this.selections.splice(index, 1);
      console.log(this.selections,"selections") 
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

selectall()
{
    console.log(this.aclassActive,"aaaaaaa5" );
          this.selall =! this.selall;
             this.showLoader.next(true);
            this.StudentsPerSchoolSub = this._hds.getStudentsPerSchool(this.regionActive,1)

                .subscribe(data => {
                    this.StudentsPerSchool$.next(data);
                    if (this.selall === true)
                    {
                     
                    
                    this.SelectAllIds = this.StudentsPerSchool$.getValue();
                       for (let i = 0; i < this.SelectAllIds.length; i++) {
                         if (this.SelectAllIds[i].idnew >= (this.pageNew-1)*3 + 1 && this.SelectAllIds[i].idnew <= this.pageNew*3)
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
      console.log(this.aclassActive,"aaaaaaamaster" );
      if (this.aclassActive === ind)
      {
             ind = -1;
      }

      this.aclassActive = ind;
      }


    public showModal(): void {
        (<any>$("#informationfeedback")).modal("show");
    }

    public hideModal(): void {
        (<any>$("#informationfeedback")).modal("hide");
    }



    calchidden(idn)
    {
      
         if (idn < (this.pageNew-1)*3 + 1 || idn > this.pageNew*3)
            return true;
         else
             return false;
      
    }

    nextpage(secsel)
    {
      if (this.pageno === this.tot_pages) 
       return;  

        this.pageno = this.pageno + 1;
       this.setActiveRegion(secsel,1,0)
      
    }


    prevpage(secsel)
    {
      if (this.pageno ===1) 
        return;  
      this.pageno = this.pageno - 1;
      this.setActiveRegion(secsel,1,0)

    }


}
