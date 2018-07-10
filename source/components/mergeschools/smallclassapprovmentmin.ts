import { Component, OnDestroy, OnInit } from "@angular/core";
import { Injectable } from "@angular/core";
import { ActivatedRoute, Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";
import { NgRedux } from "@angular-redux/store";
import { HelperDataService } from "../../services/helper-data-service";
import { LOGININFO_INITIAL_STATE } from "../../store/logininfo/logininfo.initial-state";
import { ILoginInfoRecords } from "../../store/logininfo/logininfo.types";
import { IAppState } from "../../store/store";

@Component({
    selector: "smallclassapprovementmin",
    template: `
    <div class="loading" *ngIf="(showLoader | async) === true"></div>
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


        <div style="min-height: 500px;">
        <form>

        <div *ngFor="let PdeNames$  of Pde$  | async; let k=index; let isOdd=odd; let isEven=even" style="font-size: 0.8em; font-weight: bold;">
                <li class="list-group-item isclickable" (click)="clickPde(PdeNames$.id)"
                     [class.oddout]="isOdd"
                    [class.evenout]="isEven" [class.selectedout]="pdeActive === PdeNames$.id" >
                    <div  class="col-md-12">{{PdeNames$.name}}!</div>
                 </li>


            <div *ngFor="let SchoolNames$  of SchoolsPerPerf$  | async; let i=index; let isOdd=odd; let isEven=even" style="font-size: 0.8em; font-weight: bold;" [hidden]="PdeNames$.id !== pdeActive">
                <li class="list-group-item isclickable" (click)="setActiveRegion(SchoolNames$.id)"
                     [class.oddout]="isOdd"
                    [class.evenout]="isEven" [class.selectedout]="regionActive === SchoolNames$.id" >
                    <div [class.changelistcolor]= "SchoolNames$.status === false" class="col-md-12">{{SchoolNames$.name}}</div>
                    <div class = "row" [hidden]="SchoolNames$.id !== regionActive" style="margin: 0px 2px 0px 2px;">
                        <div class="col-md-8">Τμήματα</div>
                        <div class="col-md-4">Έγκριση Ολιγομελούς</div>
                    </div>
                    </li>
                    <p></p>
                    <div class = "row" [hidden]="SchoolNames$.id !== regionActive" style="margin: 0px 2px 0px 2px;">
                         <div class="col-md-8">&nbsp;</div>
                         <div  [hidden]="SchoolNames$.id !== regionActive" class="col-md-4 pull-right" style="color: black;" >
                         <span aria-hidden="true">
                                <button type="button" class="btn-primary btn-sm pull-right" (click) ="setActiveRegion(SchoolNames$.id)">Κλείσιμο</button>
                              </span>
                         </div>
                    </div>

                    <div class = "row" *ngFor="let CoursesNames$  of CoursesPerPerf$  | async; let j=index; let isOdd2=odd; let isEven2=even"
                        [class.oddin]="isOdd2" [class.evenin]="isEven2" [class.changecolor]="calccolor(CoursesNames$.sizeconfirm,CoursesNames$.limitdown)"
                        [class.changecolorbalck]="calccolor(CoursesNames$.limitdown, CoursesNames$.sizeconfirm)"
                        [class.selectedappout]="regionActive === j"
                        [hidden]="(SchoolNames$.id !== regionActive) ||(calccolor(CoursesNames$.sizeconfirm,CoursesNames$.limitdown) === false) "
                        style="margin: 0px 2px 0px 2px;" >


                        <div class="col-md-8">{{CoursesNames$.name}}</div>
                        <div class="col-md-4">
                              <strong><label>Έγκριση Ολιγομελούς:</label> </strong>
                              <select class="form-control pull-right" #cb name="{{CoursesNames$.id}}" (change)="confirmApproved(CoursesNames$.classes,CoursesNames$.approved_id, cb, j)" >
                                  <option value="1" [selected]="CoursesNames$.approved === '1' ">Ναι</option>
                                  <option value="2" [selected]="CoursesNames$.approved === '0' ">Όχι</option>
                              </select>
                            </div>
                    </div>

            </div>
            </div>







        </form>
     </div>

   `
})

@Injectable() export default class SmallClassApprovementMin implements OnInit, OnDestroy {

    private SchoolsPerPerf$: BehaviorSubject<any>;
    private SchoolPerPerfSub: Subscription;
    private CoursesPerPerf$: BehaviorSubject<any>;
    private CoursesPerPerfSub: Subscription;
    private showLoader: BehaviorSubject<boolean>;
    private SavedSApproved$: BehaviorSubject<any>;
    private SavedSApprovedSub: Subscription;

    private regionActive = <number>-1;
    private pdeActive = <number>-1;
    private modalTitle: BehaviorSubject<string>;
    private modalText: BehaviorSubject<string>;
    private modalHeader: BehaviorSubject<string>;
    private minedu_userName: string;
    private minedu_userPassword: string;
    private loginInfo$: BehaviorSubject<ILoginInfoRecords>;
    private loginInfoSub: Subscription;
    private Pde$: BehaviorSubject<any>;
    private PdeSub: Subscription;


    constructor(
       private _ngRedux: NgRedux<IAppState>,
        private _hds: HelperDataService,
        private activatedRoute: ActivatedRoute,
        private router: Router)
    {
        this.loginInfo$ = new BehaviorSubject(LOGININFO_INITIAL_STATE);
        this.SchoolsPerPerf$ = new BehaviorSubject([{}]);
        this.Pde$ = new BehaviorSubject([{}]);
        this.CoursesPerPerf$ = new BehaviorSubject([{}]);
        this.SavedSApproved$ = new BehaviorSubject([{}]);
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

         this.loginInfoSub = this._ngRedux.select("loginInfo")
            .map(loginInfo => <ILoginInfoRecords>loginInfo)
            .subscribe(loginInfo => {
                if (loginInfo.size > 0) {
                    loginInfo.reduce(({}, loginInfoObj) => {
                        this.minedu_userName = loginInfoObj.minedu_username;
                        this.minedu_userPassword = loginInfoObj.minedu_userpassword;
                        return loginInfoObj;
                    }, {});
                }
                this.loginInfo$.next(loginInfo);
            }, error => console.log("error selecting loginInfo"));

        //this.showLoader.next(true);
        this.PdeSub = this._hds.getPde(this.minedu_userName,this.minedu_userPassword)
            .subscribe(data => {
                this.Pde$.next(data);
                this.showLoader.next(false);
            },
            error => {
                this.Pde$.next([{}]);
                console.log("Error Getting Schools");
                this.modalHeader.next("modal-header-danger");
                this.modalTitle.next("Αδυναμία άντλησης στοιχείων");
                this.modalText.next("Προέκυψε σφάλμα κατά την άντληση των στοιχείων. Παρακαλώ δοκιμάστε ξανά. Εφόσον το πρόβλημα συνεχίσει να υφίσταται, επικοινωνήστε με την ομάδα υποστήριξης.");
                this.showModal();
                this.showLoader.next(false);
            });


    }


    clickPde(ind)
    {
        (<any>$("#informationfeedback")).appendTo("body");
        if (ind === this.pdeActive) {
            ind = -1;

        }
        this.pdeActive = ind;

        this.showLoader.next(true);
        this.SchoolPerPerfSub = this._hds.getSchoolsMinistry(ind, this.minedu_userName, this.minedu_userPassword)
            .subscribe(data => {
                this.SchoolsPerPerf$.next(data);
                this.showLoader.next(false);
            },
            error => {
                this.SchoolsPerPerf$.next([{}]);
                console.log("Error Getting Schools");
                this.modalHeader.next("modal-header-danger");
                this.modalTitle.next("Αδυναμία άντλησης στοιχείων");
                this.modalText.next("Προέκυψε σφάλμα κατά την άντληση των στοιχείων. Παρακαλώ δοκιμάστε ξανά. Εφόσον το πρόβλημα συνεχίσει να υφίσταται, επικοινωνήστε με την ομάδα υποστήριξης.");
                this.showModal();
                this.showLoader.next(false);
            });
    }


    calccolor(size, limit) {
        //console.log(size, limit, "oria");
        if (size < limit)
            return true;
        else
            return false;
    }

    setActiveRegion(ind) {
        this.CoursesPerPerf$.next([{}]);
        if (ind === this.regionActive) {
            ind = -1;
            this.regionActive = ind;
        }
        else {
            this.regionActive = ind;
            this.showLoader.next(true);
            this.CoursesPerPerfSub = this._hds.getCoursePerPerfectureMin(this.regionActive,this.minedu_userName, this.minedu_userPassword)
                .subscribe(data => {
                    this.CoursesPerPerf$.next(data);
                    this.showLoader.next(false);
                },
                error => {
                    console.log("Error Getting Courses");
                    this.modalHeader.next("modal-header-danger");
                    this.modalTitle.next("Αδυναμία άντλησης στοιχείων");
                    this.modalText.next("Προέκυψε σφάλμα κατά την άντληση των στοιχείων. Παρακαλώ δοκιμάστε ξανά. Εφόσον το πρόβλημα συνεχίσει να υφίσταται, επικοινωνήστε με την ομάδα υποστήριξης.");
                    this.showModal();
                    this.showLoader.next(false);
                });
        }
    }

    confirmApproved(taxi, classid, cb, ind) {
        let rtype;
        if (cb.value === 1)
            rtype = "1";
        if (cb.value === 2)
            rtype = "0";
        let type = cb.value;

        let std = this.CoursesPerPerf$.getValue();
        std[ind].approved = rtype;
        this.SavedSApprovedSub = this._hds.saveApprovedClassesMin(taxi, classid, type,this.minedu_userName, this.minedu_userPassword).subscribe(data => {
            this.SavedSApproved$.next(data);
            this.showLoader.next(false);
          },
            error => {
                this.SavedSApproved$.next([{}]);
                console.log("Error saving Approved");
                this.showLoader.next(false);
               });
    }


    public showModal(): void {
        (<any>$("#informationfeedback")).modal("show");
    }

    public hideModal(): void {
        (<any>$("#informationfeedback")).modal("hide");
    }

}
