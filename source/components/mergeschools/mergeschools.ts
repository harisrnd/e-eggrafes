import { Component, Injectable, OnDestroy, OnInit } from "@angular/core";
import { Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";
import { HelperDataService } from "../../services/helper-data-service";
import { FormBuilder, FormGroup } from "@angular/forms";

@Component({
    selector: "mergeschools",
    template: `
             <div id="applicationDeleteConfirm" (onHidden)="onHidden()" class="modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header modal-header-danger">
                        <h3 class="modal-title pull-left"><i class="fa fa-close"></i>&nbsp;&nbsp;Συγχώνευση τμημάτων ΕΠΑΛ</h3>
                      <button type="button" class="close pull-right" aria-label="Close" (click)="hideModal()">
                        <span aria-hidden="true"><i class="fa fa-times"></i></span>
                      </button>
                    </div>
                    <div class="modal-body">
                        <p>Επιλέξατε να κάνετε συγχώνευση τμημάτων των σχολείων {{ modalText1 | async }} και {{ modalText | async }} με ταυτόχρονη μετακίνηση όλων των μαθητών στο {{ modalText | async }}.</p>
                        <p>Παρακαλούμε επιλέξτε Επιβεβαίωση</p>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default pull-left" data-dismiss="modal" (click)="hideConfirmModal()">Ακύρωση</button>
                      <button type="button" class="btn btn-default pull-left" data-dismiss="modal" (click)="mergecoursesDo()">Επιβεβαίωση</button>
                    </div>
                  </div>
                </div>
              </div>

        <div class = "loading" *ngIf="(showLoader | async) === true"></div>
        <form>
            <p style="margin-top: 20px; line-height: 2em;">Στην παρακάτω λίστα εμφανίζονται οι τομείς και ειδικότητες των σχολείων ευθύνης σας.
            <br/>Επιλέξτε τάξη/τομέα/ειδικότητα για να εμφανιστούν τα τμήματα των σχολείων που είναι ολιγομελή.</p>
            <div class="row" style="margin-top: 20px; line-height: 2em;"><p><strong>Τα τμήματα</strong></p></div>


          <div style="font-weight: bold;">
                <li class="list-group-item isclickable" (click)="setActiveclass(1)" (click)="findcoursesformerge()">
                    <div class="col-md-12"
                    [class.selectedout]="aclassActive === 1" > Ά Λυκείου  </div>
                </li>

                 <div [hidden] ="aclassActive !== 1">
                 <div *ngFor="let CoursesforMerges$  of School$ | async; let j=index; let isOdd=odd;
                  let isEven=even" class="row list-group-item isclickable"
                     [class.oddout]="isOdd" [class.evenout]="isEven" style="margin: 0px 2px 0px 2px;"
                          (click)="setActiveCourses(CoursesforMerges$.id)"
                          (click)="findmergingcourse(CoursesforMerges$.id,1,0,0)">


                            <div class="col-md-8" style="   font-weight: bold;" >{{CoursesforMerges$.name}}</div>
                            <div class="col-md-3" style="   font-weight: bold;" >{{CoursesforMerges$.studentcount}}</div>


                               <div [hidden]="courseActive !== CoursesforMerges$.id" >
                                 <div *ngFor="let AllCourses$  of CoursesforMerge$ | async; let l=index; let isOdd=odd; let isEven=even" class="row list-group-item isclickable"
                                 [class.oddout]="isOdd" [class.evenout]="isEven" style="margin: 0px 2px 0px 2px;">
                                  <div class="col-md-7" style="   font-weight: bold;" >{{AllCourses$.name}}</div>
                                  <div class="col-md-2" style="   font-weight: bold;" >{{AllCourses$.studentcount}}</div>
                                  <div class="col-md-3" style="   font-weight: bold;" >

                                      <i class="fa fa-compress isclickable" (click)="mergecourses(AllCourses$.id ,CoursesforMerges$.id,AllCourses$.name, CoursesforMerges$.name,1, 0, 0)"></i>

                                  </div>
                               </div>
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
                <li class="list-group-item isclickable" (click)="setActive(SectorNames$.id)"
                     [class.oddout]="isOdd" [class.evenout]="isEven"
                     [class.selectedout]="SectorActiveforBClass === SectorNames$.id" >
                    <div class="col-md-12">{{SectorNames$.name}}</div>
                </li>
                <div [hidden]="SectorActiveforBClass !== SectorNames$.id">
                 <div *ngFor="let CoursesforMerges$  of School$ | async; let j=index; let isOdd=odd;
                  let isEven=even" class="row list-group-item isclickable"
                     [class.oddout]="isOdd" [class.evenout]="isEven" style="font-size: 0.8em; margin: 0px 2px 0px 2px;"
                          (click)="setActiveSchoolforBClass(CoursesforMerges$.id)"
                          (click)="findmergingcourse(CoursesforMerges$.id,2,SectorNames$.id, 0)">


                    <div class="col-md-8" style="   font-weight: bold;" >{{CoursesforMerges$.name}}</div>
                    <div class="col-md-3" style="   font-weight: bold;" >{{CoursesforMerges$.studentcount}}</div>


                                  <div [hidden]="specialActive !== CoursesforMerges$.id" >
                                 <div *ngFor="let AllCourses$  of CoursesforMerge$ | async; let l=index; let isOdd=odd; let isEven=even" class="row list-group-item isclickable"
                                 [class.oddout]="isOdd" [class.evenout]="isEven" style="margin: 0px 2px 0px 2px;">
                                  <div class="col-md-7" style="   font-weight: bold;" >{{AllCourses$.name}}</div>
                                  <div class="col-md-2" style="   font-weight: bold;" >{{AllCourses$.studentcount}}</div>
                                  <div class="col-md-3" style="   font-weight: bold;" >

                                      <i class="fa fa-compress isclickable" (click)="mergecourses(AllCourses$.id ,CoursesforMerges$.id,AllCourses$.name, CoursesforMerges$.name,2, SectorNames$.id, 0)"></i>

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


                <div *ngFor="let SectorNames$  of Sectors$  | async; let i=index; let isOdd=odd; let isEven=even" style="   font-weight: bold;">
                <li class="list-group-item isclickable" (click)="setActiveSector(SectorNames$.id)"
                     [class.oddout]="isOdd" [class.evenout]="isEven" [class.selectedout]="regionActive === SectorNames$.id" >
                    <div class="col-md-12">{{SectorNames$.name}}</div>
                </li>


                      <div [hidden]="regionActive !== SectorNames$.id"  >

                          <div *ngFor="let Courses$  of Specialit$ | async; let j=index; let isOdd=odd; let isEven=even" style="font-size: 0.8em; font-weight: bold;">
                            <li class="list-group-item isclickable" (click)="setActiveSpecial(SectorNames$.id, Courses$.id)"
                                 [class.oddout]="isOdd" [class.evenout]="isEven" [class.selectedout]="courseActive === Courses$.id" >
                                <div class="col-md-12">{{Courses$.name}}</div>
                            </li>


                            <div [hidden]="courseActiveforCClass !== Courses$.id"  >
                             <div *ngFor="let CoursesforMerges$  of School$ | async;
                             let k=index; let isOdd=odd; let isEven=even"
                             class="row list-group-item isclickable"
                             [class.oddout]="isOdd" [class.evenout]="isEven"
                             style="margin: 0px 2px 0px 2px; "
                             (click)="setActiveCourses(CoursesforMerges$.id)"
                             (click)="findmergingcourse(CoursesforMerges$.id,3,SectorNames$.id, Courses$.id)">
                                  <div class="col-md-8" style="   font-weight: bold;" >{{CoursesforMerges$.name}}</div>
                                  <div class="col-md-3" style="   font-weight: bold;" >{{CoursesforMerges$.studentcount}}</div>

                                  <div [hidden]="courseActive !== CoursesforMerges$.id" >
                                 <div *ngFor="let AllCourses$  of CoursesforMerge$ | async; let l=index; let isOdd=odd; let isEven=even"
                                 class="row list-group-item"
                                 [class.oddout]="isOdd" [class.evenout]="isEven" style="margin: 0px 2px 0px 2px;">
                                  <div class="col-md-7" style="   font-weight: bold;" >{{AllCourses$.name}}</div>
                                  <div class="col-md-2" style="   font-weight: bold;" >{{AllCourses$.studentcount}}</div>
                                  <div class="col-md-3" style="   font-weight: bold;" >
                                      <i class="fa fa-compress isclickable" (click)="mergecourses(AllCourses$.id ,CoursesforMerges$.id,AllCourses$.name, CoursesforMerges$.name, 3,SectorNames$.id, Courses$.id)"></i>
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


                <div *ngFor="let SectorNames$  of Sectors$  | async; let i=index; let isOdd=odd; let isEven=even" style="   font-weight: bold;">
                <li class="list-group-item isclickable" (click)="setActiveSector(SectorNames$.id)"
                     [class.oddout]="isOdd" [class.evenout]="isEven" [class.selectedout]="regionActive === SectorNames$.id" >
                    <div class="col-md-12">{{SectorNames$.name}}</div>
                </li>


                      <div [hidden]="regionActive !== SectorNames$.id"  >

                          <div *ngFor="let Courses$  of Specialit$ | async; let j=index; let isOdd=odd; let isEven=even" style="font-size: 0.8em; font-weight: bold;">
                            <li class="list-group-item isclickable" (click)="setActiveSpecialD(SectorNames$.id, Courses$.id)"
                                 [class.oddout]="isOdd" [class.evenout]="isEven" [class.selectedout]="courseActive === Courses$.id" >
                                <div class="col-md-12">{{Courses$.name}}</div>
                            </li>


                            <div [hidden]="courseActiveforDClass !== Courses$.id"  >
                             <div *ngFor="let CoursesforMerges$  of School$ | async;
                             let k=index; let isOdd=odd; let isEven=even"
                             class="row list-group-item isclickable"
                             [class.oddout]="isOdd" [class.evenout]="isEven"
                             style="margin: 0px 2px 0px 2px; "
                             (click)="setActiveCourses(CoursesforMerges$.id)"
                             (click)="findmergingcourse(CoursesforMerges$.id,4,SectorNames$.id, Courses$.id)">
                                  <div class="col-md-8" style="   font-weight: bold;" >{{CoursesforMerges$.name}}</div>
                                  <div class="col-md-3" style="   font-weight: bold;" >{{CoursesforMerges$.studentcount}}</div>

                                  <div [hidden]="courseActive !== CoursesforMerges$.id" >
                                 <div *ngFor="let AllCourses$  of CoursesforMerge$ | async; let l=index; let isOdd=odd; let isEven=even"
                                 class="row list-group-item"
                                 [class.oddout]="isOdd" [class.evenout]="isEven" style="margin: 0px 2px 0px 2px;">
                                  <div class="col-md-7" style="   font-weight: bold;" >{{AllCourses$.name}}</div>
                                  <div class="col-md-2" style="   font-weight: bold;" >{{AllCourses$.studentcount}}</div>
                                  <div class="col-md-3" style="   font-weight: bold;" >
                                      <i class="fa fa-compress isclickable" (click)="mergecourses(AllCourses$.id ,CoursesforMerges$.id,AllCourses$.name, CoursesforMerges$.name, 4,SectorNames$.id, Courses$.id)"></i>
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

@Injectable() export default class MergeSchools implements OnInit, OnDestroy {

    private Sectors$: BehaviorSubject<any>;
    private SectorsSub: Subscription;
    private Specialit$: BehaviorSubject<any>;
    private SpecialitSub: Subscription;
    private School$: BehaviorSubject<any>;
    private SchoolSub: Subscription;
    private CoursesforMerge$: BehaviorSubject<any>;
    private CoursesforMergeSub: Subscription;
    private showLoader: BehaviorSubject<boolean>;
    private regionActive = <number>-1;
    private SectorActive = <number>-1;
    private SectorActiveforBClass = <number>-1;
    private courseActiveforCClass = <number>-1;
    private courseActiveforDClass = <number>-1;
    private specialActive = <number>-1;
    private courseActive = <number>-1;
    private aclassActive = <number>-1;
    private firstid: number;
    private secondid: number;
    private modalText: BehaviorSubject<string>;
    private modalText1: BehaviorSubject<string>;
    private sector: Number;
    private special: Number;
    private classselection :Number;


    constructor(
        private router: Router,
        private _hds: HelperDataService,
    ) {

        this.Sectors$ = new BehaviorSubject([{}]);
        this.Specialit$ = new BehaviorSubject([{}]);
        this.School$ = new BehaviorSubject([{}]);
        this.CoursesforMerge$ = new BehaviorSubject([{}]);
        this.showLoader = new BehaviorSubject(false);
        this.modalText = new BehaviorSubject("");
        this.modalText1 = new BehaviorSubject("");

    }

    public showConfirmModal(): void {
        (<any>$("#applicationDeleteConfirm")).modal("show");
    }

    public hideConfirmModal(): void {
        (<any>$("#applicationDeleteConfirm")).modal("hide");
    }

   ngOnInit()
   {
      this.findcourses();
      (<any>$("#applicationDeleteConfirm")).appendTo("body");

    }


    ngOnDestroy()
    {
        (<any>$("#applicationDeleteConfirm")).remove();
    }


    findcourses()
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





  setActiveSector(ind)
  {
        this.aclassActive = -1;
        this.SectorActiveforBClass = -1;
        this.regionActive = -1;
        this.courseActive = -1;
        this.specialActive = -1;
        this.courseActiveforCClass = -1;
        this.courseActiveforDClass = -1;
        this.Specialit$.next([{}]);
        if (ind === this.regionActive) {
            ind = -1;
            this.regionActive = ind;
            this.specialActive = ind;
            this.courseActive = ind;
        }
        else {
            this.regionActive = ind;
            this.showLoader.next(true);
            this.SpecialitSub = this._hds.getAllCourses(this.regionActive)
                .subscribe(data => {
                    this.Specialit$.next(data);
                    this.showLoader.next(false);
                },
                error => {
                    console.log("Error Getting Courses");
                    this.showLoader.next(false);
                });
        }



  }


  setActive(ind)
  {
      this.aclassActive = -1;
      this.SectorActiveforBClass = -1;
      this.regionActive = -1;
      this.courseActive = -1;
      this.specialActive = -1;
      this.courseActiveforCClass = -1;
      this.courseActiveforDClass = -1;

      this.School$.next([{}]);
        if (ind === this.SectorActiveforBClass) {
            ind = -1;
            this.SectorActiveforBClass = ind;
          }
        else {

            this.SectorActiveforBClass = ind;
            this.showLoader.next(true);
            this.SchoolSub = this._hds.FindSmallCourses(2,ind, 0)
                .subscribe(data => {
                    this.School$.next(data);
                    this.showLoader.next(false);
                                    },
                error => {
                    console.log("Error Getting Courses");
                    this.showLoader.next(false);
                });
        }
 }


  setActiveSpecial(sector_ind, special_ind){


      this.School$.next([{}]);
        if (special_ind === this.courseActiveforCClass) {
            special_ind = -1;
            this.SectorActive = sector_ind;
            this.courseActiveforCClass = special_ind;


        }
        else {

            this.SectorActive = sector_ind;
            this.courseActiveforCClass = special_ind;
            this.showLoader.next(true);
            this.SchoolSub = this._hds.FindSmallCourses(3,sector_ind, special_ind)
                .subscribe(data => {
                    this.School$.next(data);
                    this.showLoader.next(false);

                },
                error => {
                    console.log("Error Getting Courses");
                    this.showLoader.next(false);
                });
        }
  }


  setActiveSpecialD(sector_ind, special_ind){


      this.School$.next([{}]);
        if (special_ind === this.courseActiveforDClass) {
            special_ind = -1;
            this.SectorActive = sector_ind;
            this.courseActiveforDClass = special_ind;


        }
        else {

            this.SectorActive = sector_ind;
            this.courseActiveforDClass = special_ind;
            this.showLoader.next(true);
            this.SchoolSub = this._hds.FindSmallCourses(4,sector_ind, special_ind)
                .subscribe(data => {
                    this.School$.next(data);
                    this.showLoader.next(false);

                },
                error => {
                    console.log("Error Getting Courses");
                    this.showLoader.next(false);
                });
        }
  }




      setActiveCourses(ind){

        if (this.courseActive === ind) {
            ind = -1;
        }
        this.courseActive = ind;


  }



      setActiveSchoolforBClass(ind){

        if (this.specialActive === ind) {
            ind = -1;
        }
        this.specialActive = ind;


  }

  setActiveclass(ind)
  {
      this.aclassActive = -1;
      this.SectorActiveforBClass = -1;
      this.regionActive = -1;
      this.courseActive = -1;
      this.specialActive = -1;
      this.courseActiveforCClass = -1;
      this.courseActiveforDClass = -1;
      this.School$.next([{}]);
      if (this.aclassActive === ind)
      {
             ind = -1;
      }

      this.aclassActive = ind;
      }




      findmergingcourse(nid, classid, sector, special){

           this.showLoader.next(true);
           console.log(classid,"taxi");
           this.CoursesforMergeSub = this._hds.FindMergingCourses(nid, classid, sector, special).subscribe(x => {
             this.CoursesforMerge$.next(x);
             this.showLoader.next(false);

        },
            error => {
                this.CoursesforMerge$.next([{}]);
                console.log("Error Getting all Courses for a specific speciality/sector");
                this.showLoader.next(false);
            });

    }




     mergecourses(fid, sid, fname, sname,classid, sector, special){


            this.firstid = fid;
            this.secondid = sid;
            this.sector = sector;
            this.special = special;
            this.classselection = classid;
            this.modalText.next(fname);
            this.modalText1.next(sname);
            this.showConfirmModal();

     }

     mergecoursesDo()
     {

            this._hds.mergecourses(this.firstid, this.secondid, this.classselection, this.sector, this.special)
                .then(res => {
                    this.showLoader.next(false);


            this.SchoolSub = this._hds.FindSmallCourses(this.classselection,this.sector, this.special)

                    .subscribe(x => {
            this.School$.next(x);
            this.showLoader.next(false);
        },
            error => {
                this.School$.next([{}]);
                console.log("Error Getting small Courses");
                this.showLoader.next(false);
            });



                })
                .catch(err => {
                    this.showLoader.next(false);
                    console.log("err");
                });

    }




  findcoursesformerge()
  {


            this.showLoader.next(true);
            this.SchoolSub = this._hds.FindSmallCourses(1,0, 0)
                .subscribe(data => {
                    this.School$.next(data);
                    this.showLoader.next(false);
                                    },
                error => {
                    console.log("Error Getting Courses");
                    this.showLoader.next(false);
                });

 }



}
