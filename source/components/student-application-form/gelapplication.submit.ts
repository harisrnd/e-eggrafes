import { NgRedux } from "@angular-redux/store";
import { Injectable } from "@angular/core";
import { Component, OnInit } from "@angular/core";
import { Headers, Http, RequestOptions, Response } from "@angular/http";
import { Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";

import { AppSettings } from "../../app.settings";
import { IAppState } from "../../store/store";
import { HelperDataService } from "../../services/helper-data-service";


import { DataModeActions } from "../../actions/datamode.actions";
//import { EpalClassesActions } from "../../actions/epalclass.actions";
//import { RegionSchoolsActions } from "../../actions/regionschools.actions";
//import { SectorCoursesActions } from "../../actions/sectorcourses.actions";
//import { SectorFieldsActions } from "../../actions/sectorfields.actions";
//import { StudentDataFieldsActions } from "../../actions/studentdatafields.actions";
import { SchoolTypeActions } from "../../actions/schooltype.actions";
import { GelClassesActions } from "../../actions/gelclasses.actions";
import { ElectiveCourseFieldsActions } from "../../actions/electivecoursesfields.actions";
import { OrientationGroupActions } from "../../actions/orientationgroup.action";
import { GelStudentDataFieldsActions } from "../../actions/gelstudentdatafields.actions";

import { LOGININFO_INITIAL_STATE } from "../../store/logininfo/logininfo.initial-state";
import { GELCLASSES_INITIAL_STATE } from "../../store/gelclasses/gelclasses.initial-state";
import { GELSTUDENT_DATA_FIELDS_INITIAL_STATE } from "../../store/gelstudentdatafields/gelstudentdatafields.initial-state";
import { ORIENTATIONGROUP_INITIAL_STATE } from "../../store/orientationgroup/orientationgroup.initial-state";
import { ELECTIVECOURSE_FIELDS_INITIAL_STATE } from "../../store/electivecoursesfields/electivecoursesfields.initial-state";
//import { EPALCLASSES_INITIAL_STATE } from "../../store/epalclasses/epalclasses.initial-state";
//import { STUDENT_DATA_FIELDS_INITIAL_STATE } from "../../store/studentdatafields/studentdatafields.initial-state";

import { IDataModeRecords } from "../../store/datamode/datamode.types";
//import { IEpalClassRecords } from "../../store/epalclasses/epalclasses.types";
//import { ILoginInfoRecords } from "../../store/logininfo/logininfo.types";
//import { IRegionRecords } from "../../store/regionschools/regionschools.types";
//import { ISectorRecords } from "../../store/sectorcourses/sectorcourses.types";
//import { ISectorFieldRecords } from "../../store/sectorfields/sectorfields.types";
//import { IStudentDataFieldRecords } from "../../store/studentdatafields/studentdatafields.types";
import { ILoginInfoRecords } from "../../store/logininfo/logininfo.types";
import { ISchoolTypeRecords } from "../../store/schooltype/schooltype.types";
import { IGelClassRecords } from "../../store/gelclasses/gelclasses.types";
import { IElectiveCourseFieldRecords } from "../../store/electivecoursesfields/electivecoursesfields.types";
import { IOrientationGroupRecords } from "../../store/orientationgroup/orientationgroup.types";
import { IGelStudentDataFieldRecords } from "../../store/gelstudentdatafields/gelstudentdatafields.types";

import { StudentGelCourseChosen } from "../students/student";

@Component({
    selector: "gel-application-submit",
    template: `

    <div class = "loading" *ngIf="( loginInfo$ | async).size === 0 || (gelclasses$ | async).size === 0 ||
      (gelstudentDataFields$ | async).size === 0 || (showLoader | async) === true ">
    </div>

    <div id="studentFormSentNotice" (onHidden)="onHidden()" class="modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header {{modalHeader | async}}">
              <h3 class="modal-title pull-left"><i class="fa fa-check-square-o"></i>&nbsp;&nbsp;{{ modalTitle | async }}</h3>
            <button type="button" class="close pull-right" aria-label="Close" (click)="hideModal()">
              <span aria-hidden="true"><i class="fa fa-times"></i></span>
            </button>
          </div>
          <div class="modal-body">
              <p>{{ modalText | async }}</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal" (click)="hideModal()">Κλείσιμο</button>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
            <breadcrumbs></breadcrumbs>
    </div>


    <div *ngFor="let loginInfoRow$ of loginInfo$ | async; let i=index;" >
        <div class="row evenin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
            <div class="col-md-12" style="font-size: 1em; font-weight: bold; text-align: center;">Στοιχεία αιτούμενου</div>
        </div>
        <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
            <div class="col-md-3" style="font-size: 0.8em;">Όνομα</div>
            <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{ loginInfoRow$.cu_name }}</div>
            <div class="col-md-3" style="font-size: 0.8em;">Επώνυμο</div>
            <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{ loginInfoRow$.cu_surname }}</div>
        </div>
        <div class="row oddin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
            <div class="col-md-3" style="font-size: 0.8em;">Όνομα πατέρα</div>
            <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{ loginInfoRow$.cu_fathername }}</div>
            <div class="col-md-3" style="font-size: 0.8em;">Όνομα μητέρας</div>
            <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{ loginInfoRow$.cu_mothername }}</div>
        </div>
    </div>
    <div *ngFor="let gelstudentDataField$ of gelstudentDataFields$ | async;">
        <div class="row oddin" style="margin: 0px 2px 20px 2px; line-height: 2em;">
            <div class="col-md-3" style="font-size: 0.8em;">Διεύθυνση</div>
            <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{gelstudentDataField$.get("regionaddress")}}</div>
            <div class="col-md-3" style="font-size: 0.8em;">ΤΚ - Πόλη</div>
            <div class="col-md-3" style="font-size: 0.8em; font-weight: bold">{{gelstudentDataField$.get("regiontk")}} - {{gelstudentDataField$.get("regionarea")}}</div>
        </div>

        <div class="row evenin" style="margin: 20px 2px 10px 2px; line-height: 2em;">
            <div class="col-md-12" style="font-size: 1.5em; font-weight: bold; text-align: center;">Στοιχεία μαθητή</div>
        </div>
        <div><label for="name">Όνομα μαθητή</label> <p class="form-control" style="border:1px solid #eceeef;">   {{gelstudentDataField$.get("name")}} </p> </div>
        <div><label for="studentsurname">Επώνυμο μαθητή</label> <p class="form-control" style="border:1px solid #eceeef;"> {{gelstudentDataField$.get("studentsurname")}} </p></div>
        <div><label for="fatherfirstname">Όνομα Πατέρα</label> <p class="form-control" style="border:1px solid #eceeef;"> {{gelstudentDataField$.get("fatherfirstname")}} </p></div>
        <div><label for="motherfirstname">Όνομα Μητέρας</label> <p class="form-control" style="border:1px solid #eceeef;"> {{gelstudentDataField$.get("motherfirstname")}} </p></div>
        <div><label for="birthdate">Ημερομηνία Γέννησης</label> <p class="form-control" style="border:1px solid #eceeef;"> {{gelstudentDataField$.get("studentbirthdate")}} </p></div>

        <div><label for="lastschool_schoolname">Σχολείο τελευταίας φοίτησης</label> <p class="form-control" style="border:1px solid #eceeef;"> {{gelstudentDataField$.get("lastschool_schoolname").name}} </p></div>
        <div><label for="lastschool_schoolyear">Σχολικό έτος τελευταίας φοίτησης</label> <p class="form-control" style="border:1px solid #eceeef;"> {{gelstudentDataField$.get("lastschool_schoolyear")}} </p></div>

        <div *ngIf="gelstudentDataField$.get('lastschool_class') === 1"><label for="lastschool_class">Τάξη τελευταίας φοίτησης</label> <p class="form-control" style="border:1px solid #eceeef;">Α'</p></div>
        <div *ngIf="gelstudentDataField$.get('lastschool_class') === 2"><label for="lastschool_class">Τάξη τελευταίας φοίτησης</label> <p class="form-control" style="border:1px solid #eceeef;">Β'</p></div>
        <div *ngIf="gelstudentDataField$.get('lastschool_class') === 3"><label for="lastschool_class">Τάξη τελευταίας φοίτησης</label> <p class="form-control" style="border:1px solid #eceeef;">Γ'</p></div>
        <div *ngIf="gelstudentDataField$.get('lastschool_class') === 4"><label for="lastschool_class">Τάξη τελευταίας φοίτησης</label> <p class="form-control" style="border:1px solid #eceeef;">Δ'</p></div>

        <div><label for="relationtostudent">Η δήλωση προτίμησης γίνεται από</label> <p class="form-control" style="border:1px solid #eceeef;"> {{gelstudentDataField$.get("relationtostudent")}} </p></div>
        <div><label for="telnum">Τηλέφωνο επικοινωνίας</label> <p class="form-control" style="border:1px solid #eceeef;"> {{gelstudentDataField$.get("telnum")}} </p></div>
    </div>
    <div class="row" style="margin-top: 20px; margin-bottom: 20px;">
        <div class="col-md-6">
            <button type="button" class="btn-primary btn-lg pull-left" (click)="navigateBack()">
                <i class="fa fa-backward"></i>
            </button>
        </div>
        <div class="col-md-6">
            <button type="button"
                *ngIf="(app_update | async) === false &&
                 (
                 ( ( (classSelected | async) === '2' || (classSelected | async) === '3' || (classSelected | async) === '6' || (classSelected | async) === '7' ) && (orientationSelected | async) !== '-1' )  ||
                 ( ( (classSelected | async) === '1' || (classSelected | async) === '3' || (classSelected | async) === '4' ) && (courseSelected$ | async).length != 0 ) ||
                 (classSelected | async) === '5'
                 )
                 "
                class="btn-primary btn-lg pull-right isclickable" style="width: 9em;" (click)="submitNow(true)">
                <span style="font-size: 0.9em; font-weight: bold;">Υποβολή&nbsp;&nbsp;&nbsp;</span><i class="fa fa-forward"></i>
            </button>
            <button type="button" *ngIf="(app_update | async) === true" class="btn-primary btn-lg pull-right isclickable" style="width: 9em;" (click)="submitNow(false)">
                <span style="font-size: 0.9em; font-weight: bold;">Υποβολή τροποποίησης&nbsp;&nbsp;&nbsp;</span><i class="fa fa-forward"></i>
            </button>
        </div>
    </div>
  `
})

@Injectable() export default class GelApplicationSubmit implements OnInit {

    private authToken;
    private courseSelectedOrder: Array<number> = new Array();
    private courseSelectedId: Array<number> = new Array();

    private classSelected: BehaviorSubject<number>;;
    private orientationSelected: BehaviorSubject<number>;

    private courseSelected$: BehaviorSubject<Array<number>> = new BehaviorSubject(new Array());
    //private orientationSelected;

    //private electiveCoursesSelected$: BehaviorSubject<Array<number>> = new BehaviorSubject(new Array());
    //private electiveCoursesSelectedOrder: Array<number> = new Array();
    //private epalSelectedName: Array<string> = new Array();
    //private epalSelectedId: Array<string> = new Array();

    private loginInfo$: BehaviorSubject<ILoginInfoRecords>;
    private gelclasses$: BehaviorSubject<IGelClassRecords>;
    private orientationGroup$: BehaviorSubject<IOrientationGroupRecords>;
    private electivecourseFields$: BehaviorSubject<IElectiveCourseFieldRecords>;
    private gelstudentDataFields$: BehaviorSubject<IGelStudentDataFieldRecords>;

    private loginInfoSub: Subscription;
    private datamodeSub: Subscription;
    private gelclassesSub: Subscription;
    private orientationGroupSub: Subscription;
    private electivecourseFieldsSub: Subscription;
    private gelstudentDataFieldsSub: Subscription;

    private modalTitle: BehaviorSubject<string>;
    private modalText: BehaviorSubject<string>;
    private modalHeader: BehaviorSubject<string>;
    public isModalShown: BehaviorSubject<boolean>;
    private showLoader: BehaviorSubject<boolean>;

    private currentUrl: string;
    private cu_name: string;
    private cu_surname: string;
    private cu_fathername: string;
    private cu_mothername: string;
    private disclaimer_checked: number;
    private hasright: number;
    private app_update: BehaviorSubject<boolean>;
    private appId: BehaviorSubject<string>;

    constructor(
        private _hds: HelperDataService,
        private _sta: SchoolTypeActions,
        private _gca: GelClassesActions,
        private _efa: ElectiveCourseFieldsActions,
        private _oga: OrientationGroupActions,
        private _sdfa: GelStudentDataFieldsActions,
        private _ngRedux: NgRedux<IAppState>,
        private router: Router,
        private http: Http
    ) {

        this.loginInfo$ = new BehaviorSubject(LOGININFO_INITIAL_STATE);
        this.gelclasses$ = new BehaviorSubject(GELCLASSES_INITIAL_STATE);
        this.orientationGroup$ = new BehaviorSubject(ORIENTATIONGROUP_INITIAL_STATE);
        this.electivecourseFields$ = new BehaviorSubject(ELECTIVECOURSE_FIELDS_INITIAL_STATE);
        this.gelstudentDataFields$ = new BehaviorSubject(GELSTUDENT_DATA_FIELDS_INITIAL_STATE);

        this.modalTitle = new BehaviorSubject("");
        this.modalText = new BehaviorSubject("");
        this.modalHeader = new BehaviorSubject("");
        this.isModalShown = new BehaviorSubject(false);
        this.showLoader = new BehaviorSubject(false);
        this.app_update = new BehaviorSubject(false);
        this.appId = new BehaviorSubject("");
        this.orientationSelected = new BehaviorSubject(-1);
        this.classSelected = new BehaviorSubject(-1);

        this.hasright = 1;

    };

    ngOnInit() {

        (<any>$("#studentFormSentNotice")).appendTo("body");
        window.scrollTo(0, 0);

        this.loginInfoSub = this._ngRedux.select("loginInfo")
            .map(loginInfo => <ILoginInfoRecords>loginInfo)
            .subscribe(linfo => {
                if (linfo.size > 0) {
                    linfo.reduce(({ }, loginInfoObj) => {
                        this.authToken = loginInfoObj.auth_token;
                        this.cu_name = loginInfoObj.cu_name;
                        this.cu_surname = loginInfoObj.cu_surname;
                        this.cu_fathername = loginInfoObj.cu_fathername;
                        this.cu_mothername = loginInfoObj.cu_mothername;
                        this.disclaimer_checked = loginInfoObj.disclaimer_checked;
                        if ( Number(loginInfoObj.numapp_self) > 0 && Number(loginInfoObj.numapp_children) >= Number(loginInfoObj.numchildren) )
                          this.hasright = 0;

                        return loginInfoObj;
                    }, {});
                }
                this.loginInfo$.next(linfo);
            }, error => { console.log("error selecting loginInfo"); });


         this.datamodeSub = this._ngRedux.select("datamode")
                .map(datamode => <IDataModeRecords>datamode)
                .subscribe(ecs => {
                    if (ecs.size > 0) {
                        ecs.reduce(({}, datamode,i) => {
                            if (datamode.get("app_update") === true) {
                                this.app_update.next(true);
                                this.appId.next(datamode.get("appid"));
                            }
                            return datamode;
                        }, {});
                    }
                }, error => { console.log("error selecting datamode"); });

        this.gelclassesSub = this._ngRedux.select("gelclasses")
            .map(gelClasses => <IGelClassRecords>gelClasses)
            .subscribe(ecs => {
            ecs.reduce(({ }, gelclass) => {
                if (gelclass.get("selected") == true) {
                    //this.classSelected = gelclass.get("id");
                      this.classSelected.next(gelclass.get("id"));
                      console.log("Debug");
                      console.log(this.classSelected.getValue());
                    }
                return gelclass;
            }, {});
            this.gelclasses$.next(ecs);
        }, error => { console.log("error selecting gelclasses"); });

        this.orientationGroupSub = this._ngRedux.select("orientationGroup")
            .map(orientationGroup => <IOrientationGroupRecords>orientationGroup)
            .subscribe(ogs => {
                  ogs.reduce(({}, orientationgroup) => {
                    if (orientationgroup.get("selected") === true) {
                        //this.orientationSelected = orientationgroup.get("id");
                        this.orientationSelected.next(orientationgroup.get("id"));
                    }
                    return orientationgroup;
                  }, {});
                  this.orientationGroup$.next(ogs);
            }, error => { console.log("error selecting orientation"); });

        this.electivecourseFieldsSub = this._ngRedux.select("electivecourseFields")
            .map(electivecourseFields => <IElectiveCourseFieldRecords>electivecourseFields)
            .subscribe(sfds => {
                let prevSelected: Array<number> = new Array();
                sfds.reduce(({}, electivecourseField) => {
                    if (electivecourseField.get("selected") === true) {
                        prevSelected = this.courseSelected$.getValue();
                        //prevSelected[prevSelected.length] = <number>parseInt(electivecourseField.id);
                        prevSelected[prevSelected.length] = electivecourseField.id;
                        this.courseSelected$.next(prevSelected);
                        this.courseSelectedOrder.push(electivecourseField.order_id);
                        this.courseSelectedId.push(electivecourseField.id);
                    }
                    //console.log("Test");
                    //console.log(this.courseSelected$.length);
                    return electivecourseField;
                }, {});
                this.electivecourseFields$.next(sfds);
            }, error => { console.log("error selecting electivecourseFields"); });

        this.gelstudentDataFieldsSub = this._ngRedux.select("gelstudentDataFields")
            .subscribe(gelstudentDataFields => {
                this.gelstudentDataFields$.next(<IGelStudentDataFieldRecords>gelstudentDataFields);
            }, error => { console.log("error selecting gelstudentDataFields"); });


    };

    ngOnDestroy() {
        (<any>$("#studentFormSentNotice")).remove();
        if (this.gelstudentDataFieldsSub) {
            this.gelstudentDataFieldsSub.unsubscribe();
        }
        if (this.gelclassesSub) {
            this.gelclassesSub.unsubscribe();
        }
        if (this.loginInfoSub) {
            this.loginInfoSub.unsubscribe();
        }
        if (this.datamodeSub)
            this.datamodeSub.unsubscribe();
    }



    submitNow(newapp) {

        if (this.gelstudentDataFields$.getValue().size === 0 || this.gelclasses$.getValue().size === 0 || this.loginInfo$.getValue().size === 0)
            return;

        let aitisiObj: Array<any> = [];
        //let epalObj: Array<StudentEpalChosen> = [];

        let std = this.gelstudentDataFields$.getValue().get(0);

        //aitisiObj[0]: στοιχεία μαθητών
        aitisiObj[0] = <any>{};

        aitisiObj[0].name = std.get("name");
        aitisiObj[0].studentsurname = std.get("studentsurname");
        aitisiObj[0].studentbirthdate = std.get("studentbirthdate");
        aitisiObj[0].fatherfirstname = std.get("fatherfirstname");
        aitisiObj[0].motherfirstname = std.get("motherfirstname");
        aitisiObj[0].regionaddress = std.get("regionaddress");
        aitisiObj[0].regionarea = std.get("regionarea");
        aitisiObj[0].regiontk = std.get("regiontk");

        aitisiObj[0].graduation_year = 0;
        aitisiObj[0].lastschool_registrynumber = std.get("lastschool_schoolname").registry_no;
        aitisiObj[0].lastschool_schoolname = std.get("lastschool_schoolname").name;
        aitisiObj[0].lastschool_schoolyear = std.get("lastschool_schoolyear");
        aitisiObj[0].lastschool_unittypeid = std.get("lastschool_schoolname").unit_type_id;
        aitisiObj[0].lastschool_class = std.get("lastschool_class");
        aitisiObj[0].relationtostudent = std.get("relationtostudent");
        aitisiObj[0].telnum = std.get("telnum");

        aitisiObj[0].cu_name = this.cu_name;
        aitisiObj[0].cu_surname = this.cu_surname;
        aitisiObj[0].cu_fathername = this.cu_fathername;
        aitisiObj[0].cu_mothername = this.cu_mothername;
        aitisiObj[0].disclaimer_checked = this.disclaimer_checked;
        aitisiObj[0].hasright = this.hasright;
        aitisiObj[0].nextclass = this.classSelected.getValue();

        //aitisiObj[1]: ομάδα προσανατολισμού
        //$classIds = array("2", "3", "6", "7");
        let classIds = ["2", "3", "6", "7"];
        //if (aitisiObj[0]["nextclass"] === "2") {
        //if (in_array(aitisiObj[0]["nextclass"], $classIds)) {
        if (classIds.indexOf(aitisiObj[0]["nextclass"]) != -1) {
          aitisiObj[1] = <any>{};
          aitisiObj[1].choice_id = this.orientationSelected.getValue();
        }

        //aitisiObj[2]: μαθήματα επιλογής
        classIds = ["1", "3", "4"];
        if (classIds.indexOf(aitisiObj[0]["nextclass"]) != -1) {
          let courseObj: Array<StudentGelCourseChosen> = [];
          let courseSelected = this.courseSelected$.getValue();
          for (let i = 0; i < courseSelected.length; i++) {
              courseObj[i] = new StudentGelCourseChosen(null, courseSelected[i], this.courseSelectedOrder[i]);
          }
          //courseObj[courseSelected.length] = new StudentGelCourseChosen(null, this.orientationSelected.getValue(), null);
          aitisiObj["2"] = courseObj;
        }

        console.log("Debugging...");
        console.log(aitisiObj);


        /*
        let epalSelected = this.epalSelected$.getValue();
        for (let i = 0; i < epalSelected.length; i++) {
            epalObj[i] = new StudentEpalChosen(null, epalSelected[i], this.epalSelectedOrder[i]);
        }
        aitisiObj["1"] = epalObj;
        */


        /*
        if (aitisiObj[0]["currentclass"] === "2") {
            aitisiObj["3"] = new StudentSectorChosen(null, this.sectorSelected);
        } else if (aitisiObj[0]["currentclass"] === "3" || aitisiObj[0]["currentclass"] === "4") {
            aitisiObj["3"] = new StudentCourseChosen(null, this.courseSelected);
        }
        */

        //this.submitRecord(newapp, nonCheckOccupancy, aitisiObj);


        this.submitRecord(newapp, aitisiObj);


    }



    submitRecord(newapp, record) {
        let errors = {
            1004: "Όνομα μαθητή (ελάχιστο τρεις (3) χαρακτήρες)",
            1005: "Επώνυμο μαθητή (ελάχιστο τρεις (3) χαρακτήρες)",
            1006: "Όνομα Πατέρα (ελάχιστο τρεις (3) χαρακτήρες)",
            1007: "Όνομα Μητέρας (ελάχιστο τρεις (3) χαρακτήρες)",
            1008: "Διεύθυνση κατοικίας αιτούμενου",
            1009: "ΤΚ (πενταψήφιος αριθμός)",
            1010: "Πόλη/Περιοχή",
            1013: "Τάξη φοίτησης",
            1014: "Η δήλωση προτίμησης γίνεται από",
            1015: "Σταθερό Τηλέφωνο Επικοινωνίας",
            1016: "Όνομα (στοιχεία αιτούμενου)",
            1017: "Επώνυμο (στοιχεία αιτούμενου)",
            1018: "Όνομα πατέρα (στοιχεία αιτούμενου)",
            1019: "Όνομα μητέρας (στοιχεία αιτούμενου)",
            1020: "Κωδικός μονάδας σχολείου τελευταίας φοίτησης",
            1021: "Τύπος μονάδας σχολείου τελευταίας φοίτησης",
            1022: "Σχολείο τελευταίας φοίτησης",
            1023: "Τάξη τελευταίας φοίτησης"
        };
        let authTokenPost = this.authToken + ":" + this.authToken;

        let headers = new Headers({
            "Authorization": "Basic " + btoa(authTokenPost),
            "Accept": "*/*",
            "Access-Control-Allow-Credentials": "true",
            "Content-Type": "application/json",
        });

        let options = new RequestOptions({ headers: headers, method: "post", withCredentials: true });
        let connectionString = `${AppSettings.API_ENDPOINT}/gel/appsubmit`;
        if (!newapp)
          connectionString = `${AppSettings.API_ENDPOINT}/gel/appupdate/` + this.appId.getValue() ;

        this.showLoader.next(true);
        this.http.post(connectionString, record, options)
            .map((res: Response) => res.json())
            .subscribe(success => {
                (<any>$(".loading")).remove();
                this.showLoader.next(false);
                let errorCode = parseInt(success.error_code);

                let mTitle = "";
                let mText = "";
                let mHeader = "";
                switch (errorCode) {
                    case 0:
                        mTitle = "Υποβολή Δήλωσης Προτίμησης";
                        mText = "Η υποβολή της δήλωσής σας πραγματοποιήθηκε. Μπορείτε να τη δείτε και να την εκτυπώσετε από την επιλογή 'Εμφάνιση - Εκτύπωση Δήλωσης Προτίμησης'. Από την επιλογή 'Υποβληθείσες Δηλώσεις' θα μπορείτε να ενημερωθείτε όταν υπάρξει εξέλιξη σχετική με τη δήλωση σας.";
                        mHeader = "modal-header-success";
                        this._sta.initSchoolType();
                        this._gca.initGelClasses();
                        this._efa.initElectiveCourseFields();
                        this._oga.initOrientationGroup();
                        this._sdfa.initGelStudentDataFields();
                        break;
                    case 997:
                        mTitle = "Αποτυχία Υποβολής Δήλωσης Προτίμησης";
                        mText = "Έχετε ήδη υποβάλλει το σύνολο των αιτήσεων που δικαιούστε να κάνετε.";
                        mHeader = "modal-header-danger";
                        break;
                    case 998:
                        mTitle = "Αποτυχία Υποβολής Δήλωσης Προτίμησης";
                        mText = "Δεν έχετε επιλέξει Ομάδα Προσανατολισμού";
                        mHeader = "modal-header-danger";
                        break;
                    case 999:
                        mTitle = "Αποτυχία Υποβολής Δήλωσης Προτίμησης";
                        mText = "Δεν έχετε επιλέξει Μάθημα Επιλογής";
                        mHeader = "modal-header-danger";
                        break;
                    /*
                    case 1000:
                        mTitle = "Αποτυχία Υποβολής Δήλωσης Προτίμησης";
                        mText = "Δεν έχετε επιλέξει σχολεία";
                        mHeader = "modal-header-danger";
                        break;
                    */
                    case 1001:
                        mTitle = "Αποτυχία Υποβολής Δήλωσης Προτίμησης";
                        mText = "Δεν έχετε αποδεχθεί τους όρους χρήσης";
                        mHeader = "modal-header-danger";
                        break;
                    case 1002:
                        mTitle = "Αποτυχία Υποβολής Δήλωσης Προτίμησης";
                        mText = "Ελέξτε τη φόρμα σας. Υπάρχουν λάθη - ελλείψεις που δεν επιτρέπουν την υποβολή.";
                        mHeader = "modal-header-danger";
                        break;
                    case 1003:
                        mTitle = "Αποτυχία Υποβολής Δήλωσης Προτίμησης";
                        mText = "Ελέξτε τη φόρμα σας. Η ημερομηνία γέννησης δεν είναι έγκυρη.";
                        mHeader = "modal-header-danger";
                        break;
                    case 1004:
                    case 1005:
                    case 1006:
                    case 1007:
                    case 1008:
                    case 1009:
                    case 1010:
                    case 1013:
                    case 1014:
                    case 1015:
                    case 1016:
                    case 1017:
                    case 1018:
                    case 1019:
                    case 1020:
                    case 1021:
                    case 1022:
                    case 1023:
                        mTitle = "Αποτυχία Υποβολής Δήλωσης Προτίμησης";
                        mText = "Παρακαλούμε ελέγξτε τα στοιχεία που υποβάλλετε. Υπάρχουν λάθη - ελλείψεις στο πεδίο \"" + errors[errorCode] + "\"που δεν επιτρέπουν την υποβολή.";
                        mHeader = "modal-header-danger";
                        break;
                    case 3002:
                        mTitle = "Αποτυχία Υποβολής Δήλωσης Προτίμησης";
                        mText = "Το σύστημα δεν δέχεται υποβολή δηλώσεων αυτή την περίοδο.";
                        mHeader = "modal-header-danger";
                        break;
                    case 8000:
                    case 8001:
                        mTitle = "Αποτυχία Υποβολής Δήλωσης Προτίμησης";
                        mText = "Προέκυψε σφάλμα κατά τη διάρκεια ελέγχου των στοιχείων φοίτησης σας. Παρακαλώ δοκιμάστε ξανά ή προσπαθήστε αργότερα. Εάν το πρόβλημα συνεχίσει να υφίσταται, επικοινωνήστε με την ομάδα υποστήριξης.";
                        mHeader = "modal-header-danger";
                        break;
                    case 8002:
                        mTitle = "Αποτυχία Υποβολής Δήλωσης Προτίμησης";
                        mText = "Τα στοιχεία φοίτησης που υποβάλλατε δεν επικυρώθηκαν. Παρακαλώ ελέγξτε τη φόρμα σας και προσπαθήστε ξανά. Εάν το θέμα συνεχίσει να υφίσταται, επικοινωνήστε με την ομάδα υποστήριξης.";
                        mHeader = "modal-header-danger";
                        break;
                    case 8003:
                        mTitle = "Αποτυχία Υποβολής Δήλωσης Προτίμησης";
                        mText = "Τα στοιχεία φοίτησης που υποβάλλατε δεν είναι έγκυρα. Παρακαλώ ελέγξτε τη φόρμα σας και προσπαθήστε ξανά. Εάν το θέμα συνεχίσει να υφίσταται, επικοινωνήστε με την ομάδα υποστήριξης.";
                        mHeader = "modal-header-danger";
                        break;
                    case 8004:
                        mTitle = "Αποτυχία Υποβολής Δήλωσης Προτίμησης";
                        mText = "Τα στοιχεία φοίτησης που υποβάλλατε δεν είναι έγκυρα. Παρακαλώ ελέγξτε τη φόρμα σας και προσπαθήστε ξανά. Επιβεβαιώστε ότι δεν έχετε ήδη κάνει δήλωση για τον ίδιο μαθητή.";
                        mHeader = "modal-header-danger";
                        break;

                    /*
                    case 9001:
                        let schoolName = success.school_name;
                        mTitle = "Αποτυχία Υποβολής Δήλωσης Προτίμησης";
                        mText =  "Το σχολείο " + schoolName + " που επιλέξατε, δεν έχει αυτή τη στιγμή διαθέσιμες θέσεις εγγραφής. Για να ολοκληρώσετε την υποβολή της αίτησης θα πρέπει να τροποποιήσετε τις επιλογές σχολικών μονάδων που κάνατε.";
                        mHeader = "modal-header-danger";
                        break;
                    */
                    default:
                        mTitle = "Αποτυχία Υποβολής Δήλωσης Προτίμησης";
                        mText = "Ελέξτε τη φόρμα σας. Υπάρχουν λάθη - ελλείψεις που δεν επιτρέπουν την υποβολή.";
                        mHeader = "modal-header-danger";
                }

                this.modalTitle.next(mTitle);
                this.modalText.next(mText);
                this.modalHeader.next(mHeader);
                this.showModal();
                (<any>$(".loading")).remove();
                this.showLoader.next(false);

            },
            error => {
                (<any>$(".loading")).remove();
                this.modalHeader.next("modal-header-danger");
                this.modalTitle.next("Υποβολή Δήλωσης Προτίμησης");
                this.modalText.next("Η υποβολή της δήλωσης προτίμησης απέτυχε. Παρακαλούμε προσπαθήστε πάλι και αν το πρόβλημα συνεχίσει να υφίσταται, επικοινωνήστε με την ομάδα υποστήριξης.");
                this.showModal();
                this.showLoader.next(false);
                console.log("Error HTTP POST Service");
            }
            );

    }


    public showModal(): void {
        (<any>$("#studentFormSentNotice")).modal("show");
    }

    public hideModal(): void {
        (<any>$("#studentFormSentNotice")).modal("hide");
        if (this.modalHeader.getValue() === "modal-header-success") {
            this.router.navigate(["/post-submit"]);
        }

    }

    public onHidden(): void {
        this.isModalShown.next(false);
        this.router.navigate(["/post-submit"]);
    }

    navigateBack() {
        this.router.navigate(["/gelstudent-application-form-main"]);
    }

}
