import { Location } from "@angular/common";
import { Component, Injectable } from "@angular/core";
import { Router } from "@angular/router";
import { NgRedux } from "@angular-redux/store";
import { DIDE_ROLE, MINISTRY_ROLE, PDE_ROLE, SCHOOL_ROLE, STUDENT_ROLE, SCHOOLGEL_ROLE, SCHOOLGYM_ROLE } from "../../constants";
import { LOGININFO_INITIAL_STATE } from "../../store/logininfo/logininfo.initial-state";
import { ILoginInfoRecords } from "../../store/logininfo/logininfo.types";
import { IAppState } from "../../store/store";
import { LoginInfoActions } from "../../actions/logininfo.actions";
import { BehaviorSubject, Subscription } from "rxjs/Rx";


@Component({
    selector: "usefull-docs",
    template: `
        <p align="left"><strong> Υπεύθυνη Δήλωση  </strong></p>
        
    <p align="left"> Η παρακάνω Υπεύθυνη Δήλωση υποβάλλεται :</p>
    <ul>
        <li>για τα ΓΕ.Λ. στη διεύθυνση ηλεκτρονικού ταχυδρομείου:  skonstantatos@minedu.gov.gr   ή στο φαξ: 2103443390</li>

        <li>για τα ΕΠΑ.Λ. στη διεύθυνση ηλεκτρονικού ταχυδρομείου: depek_merimna@minedu.gov.gr  ή στο φαξ: 2103442217 </li>
    </ul>
        <ul class="list-group">
        <li class="list-group-item isclickable evenout"  >
            <a class="col-md-12" style="font-size: 0.8em; font-weight: bold;" href="../pdfs/files/eggrafa.pdf" target="_blank">Υπεύθυνη Δήλωση</a>
        </li>
        </ul>





        <div class="row" style="margin-top: 30px; margin-bottom: 30px;">
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

@Injectable() export default class UsefullDocs {

    private authToken: string;
    private studentRole = STUDENT_ROLE;
    private authRole: string;
    private cuName: string;
    private loginInfo$: BehaviorSubject<ILoginInfoRecords>;
    private loginInfoSub: Subscription;
    private hasvalue: boolean;
  


    constructor(private router: Router,
                private loc: Location,
                private _ata: LoginInfoActions,
                private _ngRedux: NgRedux<IAppState>,


                ) {

        this.authToken = "";
        this.authRole = "";
        this.loginInfo$ = new BehaviorSubject(LOGININFO_INITIAL_STATE);
        this.hasvalue = false;


    }


    ngOnInit() {
                 

        this.loginInfoSub = this._ngRedux.select("loginInfo")
            .map(loginInfo => <ILoginInfoRecords>loginInfo)
            .subscribe(loginInfo => {
                if (loginInfo.size > 0) {
                    loginInfo.reduce(({}, loginInfoObj) => {
                        this.authToken = loginInfoObj.auth_token;
                        this.authRole = loginInfoObj.auth_role;
                        return loginInfoObj;
                    }, {});

                    if (this.authRole === SCHOOL_ROLE || this.authRole === SCHOOLGEL_ROLE || this.authRole === SCHOOLGYM_ROLE) {
                        console.log(this.authRole,"role");
                        this.hasvalue = true;

                    }
                }
                this.loginInfo$.next(loginInfo);
            });

    }

    public goBack(): void {
        this.loc.back();

    }

    public goHome(): void {
        this.router.navigate([""]);
    }



}
