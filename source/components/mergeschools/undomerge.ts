import { Component, Injectable, OnDestroy, OnInit } from "@angular/core";
import { Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";
import { HelperDataService } from "../../services/helper-data-service";

@Component({
    selector: "undomergeschools",
    template: `


                <div id="undoMergeConfirm" (onHidden)="onHidden()" class="modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header-danger">
                        <h3 class="modal-title pull-left"><i class="fa fa-close"></i>&nbsp;&nbsp;Αναίρεση Συνένωσης τμημάτων ΕΠΑΛ</h3>
                      <button type="button" class="close pull-right" aria-label="Close" (click)="hideModal()">
                        <span aria-hidden="true"><i class="fa fa-times"></i></span>
                      </button>
                    </div>
                    <div class="modal-body">
                        <p>Επιλέξατε να αναιρέσετε τη συνένωση τμημάτων των σχολείων {{ modalText1 | async }} και {{ modalText | async }}.</p>
                        <p>Παρακαλούμε επιλέξτε Επιβεβαίωση</p>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default pull-left" data-dismiss="modal" (click)="hideModal()">Ακύρωση</button>
                      <button type="button" class="btn btn-default pull-left" data-dismiss="modal" (click)="undomergeDo()">Επιβεβαίωση</button>
                    </div>
                  </div>
                </div>
              </div>


                <div id="undoAllNotice" (onHidden)="onHidden()" class="modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header-success">
                        <h3 class="modal-title pull-left"><i class="fa fa-close"></i>&nbsp;&nbsp;Επιτυχής Αναίρεση Συνενώσεων </h3>
                      <button type="button" class="close pull-right" aria-label="Close" (click)="hideModal()">
                        <span aria-hidden="true"><i class="fa fa-times"></i></span>
                      </button>
                    </div>
                    <div class="modal-body">
                        <p>Η αναίρεση συνενώσεων ήταν επιτυχής</p>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default pull-left" data-dismiss="modal" (click)="hideModal()">Κλείσιμο</button>
                      </div>
                  </div>
                </div>
              </div>


                  <div id="UndoMergeError" (onHidden)="onHidden()" class="modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header modal-header-danger">
                          <h3 class="modal-title pull-left"><i class="fa fa-ban"></i>&nbsp;&nbsp;Αποτυχία Αναίρεσης Συνένωσης</h3>
                        <button type="button" class="close pull-right" aria-label="Close" (click)="hideModal()">
                          <span aria-hidden="true"><i class="fa fa-times"></i></span>
                        </button>
                      </div>
                      <div class="modal-body">
                          <p>Δεν έχει πραγματοποιηθεί η αναίρεση συνένωσης!</p>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal" (click)="hideErrorModal()">Κλείσιμο</button>
                      </div>
                    </div>
                  </div>
                </div>


                <div id="undoAllConfirm" (onHidden)="onHidden('#undoAllConfirm')" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header-danger">
                      <h3 class="modal-title pull-left"><i class="fa fa-check-square-o"></i>&nbsp;&nbsp;Αναίρεση όλων των συνενώσεων </h3>
                      <button type="button" class="close pull-right" aria-label="Close" (click)="hideModal()">
                        <span aria-hidden="true"><i class="fa fa-times"></i></span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <p>Επιλέξατε να αναιρέσετε όλες τις συνενώσεις </p>
                      <p>Παρακαλούμε επιλέξτε Επιβεβαίωση</p>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal" (click)="hideModal()">Ακύρωση</button>
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal" (click)="undoallDo()">Επιβεβαίωση</button>
                    </div>
                  </div>
                </div>
              </div>




        <div class = "loading" *ngIf="(showLoader | async) === true"></div>
        <form>
        <div class="col-md-12">
            <p style="margin-top: 20px; line-height: 2em;">Στην παρακάτω λίστα εμφανίζονται οι τομείς και οι ειδικότητες των σχολείων ευθύνης σας.
            <br/>Επιλέξτε τάξη/τομέα/ειδικότητα για να εμφανιστούν τα τμήματα των σχολείων που έχουν συνενωθεί.
             Στη συνέχεια μπορείτε να αναιρέσετε τις συννενώσεις που θέλετε επιλέγοντας την αντίστοιχη τάξη/τομέα/ειδικότητα
             ή να αναιρέσετε όλες τις συνενώσεις συγχρόνως.</p>


                 <button type="submit" class="btn-primary btn-lg btn-block isclickable  pull-right" style="margin: 0px; font-size: 1em; padding: 5px;" (click)="undoall()">
                Αναίρεση όλων
                </button>
            </div>

          <div class="row" style="margin-top: 20px; line-height: 2em;">
           <p><strong>Τα τμήματα</strong></p></div>


              <div style="font-weight: bold;">
                <li class="list-group-item isclickable" (click)="checkclass(1)"  (click)="setActiveclass(1)">
                    <div class="col-md-12"  [class.selectedout]="aclassActive === 1" > Ά Λυκείου  </div>
                </li>

                 <div [hidden] ="aclassActive !== 1">
                       <div class="row"  style="line-height: 2em;">
                              <div class="col-md-5" style="font-weight: bold;" > <strong>Αρχικό Ολιγομελές Τμήμα</strong></div>
                              <div class="col-md-5" style="font-weight: bold;" > <strong>Σχολείο Προορισμού</strong></div>

                        </div>

                       <div *ngIf="(retrievedSch | async)">
                       <div *ngFor="let CoursesForUndoMerges$  of CoursesForUndoMerge$ | async; let i=index; let isOdd=odd; let isEven=even" >
                            <li class="list-group-item isclickable" (click)="setActive(i)"
                             [class.oddout]="isOdd" [class.evenout]="isEven"  [class.selectedout]="courseActive === i">
                              <div class="row"  style="line-height: 2em;">
                              <div class="col-md-5" style="font-weight: bold;" >{{CoursesForUndoMerges$.name}}</div>
                              <div class="col-md-5" style="font-weight: bold;" >{{CoursesForUndoMerges$.namenew}}</div>
                              <div class="col-md-2" style="font-size: 0.8em; font-weight: bold;" >

                                    <i  class="fa fa-undo isclickable" (click)="undomergecourses(CoursesForUndoMerges$.id, CoursesForUndoMerges$.idnew,
                                    CoursesForUndoMerges$.name, CoursesForUndoMerges$.namenew, 1,0,0)"></i>

                              </div>


                            </div>

                            </li>
                          </div>
                      </div>


                 </div>

          </div>
           <br>

           <div style="   font-weight: bold;">
                <li class="list-group-item isclickable" >
                    <div class="col-md-12">Β Λυκείου</div>
                </li>
           </div>

            <div *ngFor="let SectorNames$  of Sectors$  | async; let i=index;
            let isOdd=odd; let isEven=even" style="   font-weight: bold;">
                <li class="list-group-item isclickable" (click)="setActiveSectorBClass(SectorNames$.id )"
                     [class.oddout]="isOdd" [class.evenout]="isEven"
                     [class.selectedout]="SectorActiveforBClass === SectorNames$.id" >
                    <div class="col-md-12">{{SectorNames$.name}}</div>
                </li>
                <div [hidden]="SectorActiveforBClass !== SectorNames$.id">


                  <div *ngIf="(retrievedSch | async)">
                     <div *ngFor="let CoursesForUndoMerges$  of CoursesForUndoMerge$ | async; let i=index; let isOdd=odd; let isEven=even"
                      style="font-size: 0.8em; font-weight: bold;">
                            <div class="list-group-item isclickable" (click)="setActive(i)"
                             [class.oddout]="isOdd" [class.evenout]="isEven"  [class.selectedout]="courseActive === i">
                              <div class="row"  style="line-height: 2em;">
                              <div class="col-md-5" style="font-weight: bold;" >{{CoursesForUndoMerges$.name}}</div>
                              <div class="col-md-5" style="font-weight: bold;" >{{CoursesForUndoMerges$.namenew}}</div>
                              <div class="col-md-2" style="font-size: 0.8em; font-weight: bold;" >

                                   <i  class="fa fa-undo isclickable" (click)="undomergecourses(CoursesForUndoMerges$.id, CoursesForUndoMerges$.idnew,
                                    CoursesForUndoMerges$.name, CoursesForUndoMerges$.namenew, 2,SectorNames$.id,0)"></i>

                              </div>


                            </div>

                            </div>
                          </div>
                      </div>








                </div>
            </div>

            <br>
            <br>
            <div style="   font-weight: bold;">
                <li class="list-group-item isclickable" >
                    <div class="col-md-12">Γ Λυκείου</div>
                </li>
           </div>
           <br>


                <div *ngFor="let SectorNames$  of Sectors$  | async; let i=index; let isOdd=odd; let isEven=even" style="font-weight: bold;">
                <li class="list-group-item isclickable" (click)="setActiveSectorCClass(SectorNames$.id)"
                     [class.oddout]="isOdd" [class.evenout]="isEven" [class.selectedout]="SectorActiveforCClass === SectorNames$.id" >
                    <div class="col-md-12">{{SectorNames$.name}}</div>
                </li>


                      <div [hidden]="SectorActiveforCClass !== SectorNames$.id"  >

                          <div *ngFor="let Courses$  of Specialit$ | async; let j=index; let isOdd=odd; let isEven=even" style="font-size: 0.8em; font-weight: bold;">
                            <li class="list-group-item isclickable" (click)="setActiveSpecial(Courses$.id)"
                                 [class.oddout]="isOdd" [class.evenout]="isEven" [class.selectedout]="courseActive === Courses$.id" >
                                <div class="col-md-12">{{Courses$.name}}</div>
                            </li>


                            <div [hidden]="courseActive !== Courses$.id"  >



                         <div *ngIf="(retrievedSch | async)">
                           <div *ngFor="let CoursesForUndoMerges$  of CoursesForUndoMerge$ | async; let i=index;
                           let isOdd=odd; let isEven=even" >
                            <div class="list-group-item isclickable" (click)="setActive(i)"
                             [class.oddout]="isOdd" [class.evenout]="isEven"  [class.selectedout]="courseActive === i">
                              <div class="row"  style="margin: 0px 2px 0px 2px; line-height: 2em;">
                              <div class="col-md-5" style="font-weight: bold;" >{{CoursesForUndoMerges$.name}}</div>
                              <div class="col-md-5" style="font-weight: bold;" >{{CoursesForUndoMerges$.namenew}}</div>
                              <div class="col-md-2" style="font-size: 0.8em; font-weight: bold;" >

                                   <i  class="fa fa-undo isclickable" (click)="undomergecourses(CoursesForUndoMerges$.id, CoursesForUndoMerges$.idnew,
                                    CoursesForUndoMerges$.name, CoursesForUndoMerges$.namenew, 3,SectorNames$.id, Courses$.id)"></i>

                              </div>


                            </div>

                            </div>
                          </div>
                      </div>






                            </div>
                         </div>


                    </div>


              </div>



               <br>
            <br>
            <div style="   font-weight: bold;">
                <li class="list-group-item isclickable" >
                    <div class="col-md-12">Δ Λυκείου</div>
                </li>
           </div>
           <br>


                <div *ngFor="let SectorNames$  of Sectors$  | async; let i=index; let isOdd=odd; let isEven=even" style="font-weight: bold;">
                <li class="list-group-item isclickable" (click)="setActiveSectorDClass(SectorNames$.id)"
                     [class.oddout]="isOdd" [class.evenout]="isEven" [class.selectedout]="SectorActiveforDClass === SectorNames$.id" >
                    <div class="col-md-12">{{SectorNames$.name}}</div>
                </li>


                      <div [hidden]="SectorActiveforDClass !== SectorNames$.id"  >

                          <div *ngFor="let Courses$  of Specialit$ | async; let j=index; let isOdd=odd; let isEven=even" style="font-size: 0.8em; font-weight: bold;">
                            <li class="list-group-item isclickable" (click)="setActiveSpecialD(Courses$.id)"
                                 [class.oddout]="isOdd" [class.evenout]="isEven" [class.selectedout]="courseActive === Courses$.id" >
                                <div class="col-md-12">{{Courses$.name}}</div>
                            </li>


                            <div [hidden]="courseActive !== Courses$.id"  >



                         <div *ngIf="(retrievedSch | async)">
                           <div *ngFor="let CoursesForUndoMerges$  of CoursesForUndoMerge$ | async; let i=index;
                           let isOdd=odd; let isEven=even" >
                            <div class="list-group-item isclickable" (click)="setActive(i)"
                             [class.oddout]="isOdd" [class.evenout]="isEven"  [class.selectedout]="courseActive === i">
                              <div class="row"  style="margin: 0px 2px 0px 2px; line-height: 2em;">
                              <div class="col-md-5" style="font-weight: bold;" >{{CoursesForUndoMerges$.name}}</div>
                              <div class="col-md-5" style="font-weight: bold;" >{{CoursesForUndoMerges$.namenew}}</div>
                              <div class="col-md-2" style="font-size: 0.8em; font-weight: bold;" >

                                   <i  class="fa fa-undo isclickable" (click)="undomergecourses(CoursesForUndoMerges$.id, CoursesForUndoMerges$.idnew,
                                    CoursesForUndoMerges$.name, CoursesForUndoMerges$.namenew, 4,SectorNames$.id, Courses$.id)"></i>

                              </div>


                            </div>

                            </div>
                          </div>
                      </div>






                            </div>
                         </div>


                    </div>


              </div>



        </form>

   `
})

@Injectable() export default class UndoMergeSchools implements OnInit, OnDestroy {


  private Sectors$: BehaviorSubject<any>;
  private SectorsSub: Subscription;
  private Specialit$: BehaviorSubject<any>;
  private SpecialitSub: Subscription;
  private SectorSelections$: BehaviorSubject<any>;
  private CourseSelections$: BehaviorSubject<any>;
  private CoursesForUndoMerge$ : BehaviorSubject <any>;
  private CoursesforMerge$ : BehaviorSubject <any>;
  private UndoMerge$ : BehaviorSubject <any>;
  private SectorSelectionsSub: Subscription;
  private CourseSelectionsSub: Subscription;
  private CoursesForUndoMergeSub : Subscription;
  private CoursesforMergeSub : Subscription;
  private UndoMergeSub : Subscription;
  private showSectorList: BehaviorSubject<boolean>;
  private showCourseList: BehaviorSubject<boolean>;
  private showLoader: BehaviorSubject<boolean>;
  private retrievedSch : BehaviorSubject<boolean>;
  private courseActive = <number>-1;
  private modalText: BehaviorSubject<string>;
  private modalText1: BehaviorSubject<string>;
  private sector: Number;
  private special: Number;
  private classselection :Number;
  private classSelected: number;
  private sectorSelected: number;
  private courseSelected: number;
  private firstid: number;
  private secondid: number;
  private aclassActive = <number>-1;
  private SectorActiveforBClass = <number>-1;
  private SectorActiveforCClass = <number>-1;
  private SectorActiveforDClass = <number>-1;

    constructor(

        private router: Router,
        private _hds: HelperDataService,
    ) {



        this.Sectors$ = new BehaviorSubject([{}]);
        this.Specialit$ = new BehaviorSubject([{}]);
        this.showSectorList = new BehaviorSubject(false);
        this.showCourseList = new BehaviorSubject(false);
        this.SectorSelections$ = new BehaviorSubject([{}]);
        this.CourseSelections$ = new BehaviorSubject([{}]);
        this.CoursesForUndoMerge$ = new BehaviorSubject([{}])
        this.CoursesforMerge$ = new BehaviorSubject([{}])
        this.UndoMerge$ = new BehaviorSubject([{}])
        this.showLoader = new BehaviorSubject(false);
        this.retrievedSch = new BehaviorSubject(false);
        this.classSelected = 0;
        this.modalText = new BehaviorSubject("");
        this.modalText1 = new BehaviorSubject("");

    }


    public showConfirmModal(messageid): void {
        (<any>$(messageid)).modal("show");
    }

    public showErrorModal(): void {
        (<any>jQuery("#UndoMergeError")).modal("show");
    }


    public hideErrorModal(): void {
        (<any>jQuery("#UndoMergeError")).modal("hide");
    }



    ngOnDestroy()
    {
        (<any>$("#undoMergeConfirm")).remove();
        (<any>jQuery("#UndoMergeError")).remove();
        (<any>jQuery("#undoAllConfirm")).remove();
        (<any>jQuery("#undoAllNotice")).remove();

    }

    ngOnInit() {
        this.findsectors();
        this.retrievedSch.next(false);
        (<any>$("#undoMergeConfirm")).appendTo("body");
        (<any>$("#undoAllConfirm")).appendTo("body");
        (<any>$("#undoAllNotice")).appendTo("body");


        (<any>jQuery("#UndoMergeError")).appendTo("body");
    }





    checkclass(classId) {

        this.retrievedSch.next(false);
        this.classSelected = classId;
        this.sectorSelected = 0;
        this.courseSelected = 0;
        this.findcourses();
    }


    setActiveclass(ind)
    {
      this.SectorActiveforBClass = -1;
      this.SectorActiveforCClass = -1;
      this.SectorActiveforDClass = -1;
      this.courseActive = -1;
      if (this.aclassActive === ind)
      {
             ind = -1;
      }

      this.aclassActive = ind;
      }






    findcourses()
    {
       this.showLoader.next(true);
       this.CoursesForUndoMergeSub = this._hds.FindSmallCoursesforUdoMerging(this.classSelected, this.sectorSelected, this.courseSelected).subscribe(x => {
            this.CoursesForUndoMerge$.next(x);
            this.showLoader.next(false);
            this.retrievedSch.next(true);
        },
            error => {
                this.CoursesForUndoMerge$.next([{}]);
                console.log("Error Getting small Courses");
                this.showLoader.next(false);
                 this.retrievedSch.next(false);
            });
    }


    findsectors()
    {

       this.showLoader.next(true);
       this.SectorsSub = this._hds.getAllSectors().subscribe(x => {
            this.Sectors$.next(x);
               this.showLoader.next(false);
                    },
            error => {
                this.Sectors$.next([{}]);
                console.log("Error Getting sectors");
                this.showLoader.next(false);

            });
    }




    setActive(ind) {

        if (this.courseActive === ind) {
            ind = -1;
        }
        this.courseActive = ind;
    }


    setActiveSectorBClass(ind)
    {

      this.SectorActiveforCClass = -1;
       this.SectorActiveforDClass = -1;
      this.courseActive = -1;
      this.aclassActive =-1;

      if (this.SectorActiveforBClass  === ind){
            ind = -1
      }
      this.SectorActiveforBClass = ind;
      this.classSelected = 2;
      this.sectorSelected = ind;
      this.courseSelected = 0;
      this.findcourses();

    }


    setActiveSectorCClass(ind)
    {
      this.SectorActiveforBClass = -1;
      this.SectorActiveforDClass = -1;
      this.courseActive = -1;
      this.aclassActive =-1;

      if (this.SectorActiveforCClass  === ind){
            ind = -1
      }
      this.SectorActiveforCClass = ind;
      this.classSelected = 3;
      this.sectorSelected = ind;
      this.courseSelected = 0;
      this.SpecialitSub = this._hds.getAllCourses(this.SectorActiveforCClass)
                .subscribe(data => {
                    this.Specialit$.next(data);
                    this.showLoader.next(false);
                },
                error => {
                    console.log("Error Getting Courses");
                    this.showLoader.next(false);
                });

    }



    setActiveSectorDClass(ind)
    {
      this.SectorActiveforBClass = -1;
      this.SectorActiveforCClass = -1;
      this.courseActive = -1;
      this.aclassActive =-1;

      if (this.SectorActiveforDClass  === ind){
            ind = -1
      }
      this.SectorActiveforDClass = ind;
      this.classSelected = 4;
      this.sectorSelected = ind;
      this.courseSelected = 0;
      this.SpecialitSub = this._hds.getAllCourses(this.SectorActiveforDClass)
                .subscribe(data => {
                    this.Specialit$.next(data);
                    this.showLoader.next(false);
                },
                error => {
                    console.log("Error Getting Courses");
                    this.showLoader.next(false);
                });

    }



    setActiveSpecial(ind )
    {
      if (this.courseActive  === ind){
            ind = -1
      }
      this.courseActive = ind;
      this.classSelected = 3;
      this.courseSelected = ind;
      this.findcourses();



    }


    setActiveSpecialD(ind )
    {
      if (this.courseActive  === ind){
            ind = -1
      }
      this.courseActive = ind;
      this.classSelected = 4;
      this.courseSelected = ind;
      this.findcourses();



    }




      undomergecourses(nid1, nid2,fname, sname, classSelection, sector, special){


            this.firstid = nid1;
            this.secondid = nid2;
            this.sector = sector;
            this.special = special;
            this.classselection = classSelection;
            this.modalText.next(fname);
            this.modalText1.next(sname);
            this.showConfirmModal('#undoMergeConfirm');
          }

          undomergeDo(){
          this._hds.UndoMerge(this.firstid, this.secondid, this.classselection, this.sector, this.special)
                .then(res => {
              this.CoursesForUndoMergeSub = this._hds.FindSmallCoursesforUdoMerging(this.classselection, this.sector, this.special).subscribe(x => {
              this.CoursesForUndoMerge$.next(x);
              this.showLoader.next(false);
              this.showConfirmModal('#undoAllNotice');
          },
              error => {
                  this.CoursesForUndoMerge$.next([{}]);
                  console.log("Error Getting small Courses");
                  this.showLoader.next(false);

              });

                })
                .catch(err => {
                    this.showLoader.next(false);
                    this.showErrorModal();
                    console.log("error undoMerge");
                });

     }



     undoall()
     {
       this.showConfirmModal('#undoAllConfirm');
     }

     undoallDo()
     {
       this.showLoader.next(true);
       this._hds.UndoMergAll().then(res => {
                   this.showLoader.next(false);
                   this.showConfirmModal('#undoAllNotice');

                })
                .catch(err => {
                    this.showLoader.next(false);
                    this.showErrorModal();
                    console.log("error undoMerge");
                });

     }

}
