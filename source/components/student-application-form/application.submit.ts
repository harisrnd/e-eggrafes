import { NgRedux } from "@angular-redux/store";
import { Injectable } from "@angular/core";
import { Component, OnInit } from "@angular/core";
import { Headers, Http, RequestOptions, Response } from "@angular/http";
import { Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";

import { IDataModeRecords } from "../../store/datamode/datamode.types";
import { DataModeActions } from "../../actions/datamode.actions";
import { EpalClassesActions } from "../../actions/epalclass.actions";
import { RegionSchoolsActions } from "../../actions/regionschools.actions";
import { SectorCoursesActions } from "../../actions/sectorcourses.actions";
import { SectorFieldsActions } from "../../actions/sectorfields.actions";
import { StudentDataFieldsActions } from "../../actions/studentdatafields.actions";
import { AppSettings } from "../../app.settings";
import { HelperDataService } from "../../services/helper-data-service";
import { EPALCLASSES_INITIAL_STATE } from "../../store/epalclasses/epalclasses.initial-state";
import { IEpalClassRecords } from "../../store/epalclasses/epalclasses.types";
import { LOGININFO_INITIAL_STATE } from "../../store/logininfo/logininfo.initial-state";
import { ILoginInfoRecords } from "../../store/logininfo/logininfo.types";
import { IRegionRecords } from "../../store/regionschools/regionschools.types";
import { ISectorRecords } from "../../store/sectorcourses/sectorcourses.types";
import { ISectorFieldRecords } from "../../store/sectorfields/sectorfields.types";
import { IAppState } from "../../store/store";
import { STUDENT_DATA_FIELDS_INITIAL_STATE } from "../../store/studentdatafields/studentdatafields.initial-state";
import { IStudentDataFieldRecords } from "../../store/studentdatafields/studentdatafields.types";
import { StudentCourseChosen, StudentEpalChosen, StudentSectorChosen } from "../students/student";

@Component({
    selector: "application-submit",
    template: `
    <div class = "loading" *ngIf="(studentDataFields$ | async).size === 0 || (epalSelected$ | async).length === 0 || (epalclasses$ | async).size === 0 || (loginInfo$ | async).size === 0 || (showLoader | async) === true"></div>
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

<!-- <application-preview-select></application-preview-select> -->

    <div *ngFor="let loginInfoRow$ of loginInfo$ | async; let i=index;" >
        <div class="row evenin" style="margin: 0px 2px 0px 2px; line-height: 2em;">
            <div class="col-md-12" style="font-size: 1.5em; font-weight: bold; text-align: center;">Στοιχεία αιτούμενου</div>
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


    <div *ngFor="let studentDataField$ of studentDataFields$ | async;">
    <div class="row evenin" style="margin: 20px 2px 10px 2px; line-height: 2em;">
        <div class="col-md-12" style="font-size: 1.5em; font-weight: bold; text-align: center;">Στοιχεία φοίτησης μαθητή</div>
    </div>
    <div><label for="lastschool_schoolyear">Σχολικό έτος τελευταίας φοίτησης</label> <p class="form-control" style="border:1px solid #eceeef;"> {{studentDataField$.get("lastschool_schoolyear")}} </p></div>
    <div><label for="lastschool_schoolname">Σχολείο τελευταίας φοίτησης</label> <p class="form-control" style="border:1px solid #eceeef;"> {{studentDataField$.get("lastschool_schoolname").name}} </p></div>


    <div class="row evenin" style="margin: 20px 2px 10px 2px; line-height: 2em;">
        <div class="col-md-12" style="font-size: 1.5em; font-weight: bold; text-align: center;">Προσωπικά Στοιχεία μαθητή</div>
    </div>
    <div>
        <label *ngIf="studentDataField$.get('lastschool_schoolyear') >= '2013-2014' && (wsEnabled | async) === 1" for="am">Αριθμός Μητρώου Μαθητη</label>
        <p *ngIf="studentDataField$.get('lastschool_schoolyear') >= '2013-2014' && (wsEnabled | async) === 1" class="form-control" style="border:1px solid #eceeef;">   {{studentDataField$.get("am")}} </p>
    </div>
    <div><label for="name">Όνομα μαθητή</label> <p class="form-control" style="border:1px solid #eceeef;">   {{studentDataField$.get("name")}} </p> </div>
    <div><label for="studentsurname">Επώνυμο μαθητή</label> <p class="form-control" style="border:1px solid #eceeef;"> {{studentDataField$.get("studentsurname")}} </p></div>
    <div><label for="fatherfirstname">Όνομα Πατέρα</label> <p class="form-control" style="border:1px solid #eceeef;"> {{studentDataField$.get("fatherfirstname")}} </p></div>
    <div><label for="motherfirstname">Όνομα Μητέρας</label> <p class="form-control" style="border:1px solid #eceeef;"> {{studentDataField$.get("motherfirstname")}} </p></div>
    <div><label for="birthdate">Ημερομηνία Γέννησης</label> <p class="form-control" style="border:1px solid #eceeef;"> {{studentDataField$.get("studentbirthdate")}} </p></div>


    <div class="row evenin" style="margin: 20px 2px 10px 2px; line-height: 2em;">
        <div class="col-md-12" style="font-size: 1.5em; font-weight: bold; text-align: center;">Στοιχεία Επικοινωνίας μαθητή</div>
    </div>
    <table class="col-md-12" align="left" *ngIf="studentDataField$.get('lastschool_schoolyear') < '2013-2014' || (wsEnabled | async) === 0">
        <tr>
            <td>
                <div><label for="regionaddress">Διεύθυνση Κατοικίας μαθητή</label></div>
            </td>
            <td>
                <div><label for="regiontk">Τ.Κ.</label></div>
            </td>
            <td>
                <div><label for="regionarea">Πόλη/Περιοχή</label></div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="form-control" style="border:1px solid #eceeef;">{{studentDataField$.get("regionaddress")}}</div>
            </td>
            <td>
                <div class="form-control" style="border:1px solid #eceeef;">{{studentDataField$.get("regiontk")}}</div>
            </td>
            <td>
                <div class="form-control" style="border:1px solid #eceeef;">{{studentDataField$.get("regionarea")}}</div>
            </td>
        </tr>
    </table>
    <div><label for="relationtostudent">Η δήλωση προτίμησης γίνεται από</label> <p class="form-control" style="border:1px solid #eceeef;"> {{studentDataField$.get("relationtostudent")}} </p></div>
    <div><label for="telnum">Τηλέφωνο επικοινωνίας</label> <p class="form-control" style="border:1px solid #eceeef;"> {{studentDataField$.get("telnum")}} </p></div>

    </div>

    <div class="row" style="margin-top: 20px; margin-bottom: 20px;">
        <div class="col-md-6">
            <button type="button" class="btn-primary btn-lg pull-left" (click)="navigateBack()">
                <i class="fa fa-backward"></i>
            </button>
        </div>
        <div class="col-md-6">
            <button type="button" *ngIf="(app_update | async) === false" class="btn-primary btn-lg pull-right isclickable" style="width: 9em;" (click)="submitNow(true)">
                <span style="font-size: 0.9em; font-weight: bold;">Υποβολή&nbsp;&nbsp;&nbsp;</span><i class="fa fa-forward"></i>
            </button>
            <button type="button" *ngIf="(app_update | async) === true" class="btn-primary btn-lg pull-right isclickable" style="width: 9em;" (click)="submitNow(false)">
                <span style="font-size: 0.9em; font-weight: bold;">Υποβολή τροποποίησης&nbsp;&nbsp;&nbsp;</span><i class="fa fa-forward"></i>
            </button>
        </div>
    </div>
  `
})

@Injectable() export default class ApplicationSubmit implements OnInit {

    private authToken;
    private epalSelected$: BehaviorSubject<Array<number>> = new BehaviorSubject(new Array());
    private epalSelectedOrder: Array<number> = new Array();
    private courseSelected;
    private sectorSelected;
    private classSelected;
    //private courseSelectedName;
    //private sectorSelectedName;
    private epalSelectedName: Array<string> = new Array();
    private epalSelectedId: Array<string> = new Array();
    private studentDataFields$: BehaviorSubject<IStudentDataFieldRecords>;
    private epalclasses$: BehaviorSubject<IEpalClassRecords>;
    private loginInfo$: BehaviorSubject<ILoginInfoRecords>;
    private studentDataFieldsSub: Subscription;
    private regionsSub: Subscription;
    private sectorsSub: Subscription;
    private sectorFieldsSub: Subscription;
    private epalclassesSub: Subscription;
    private loginInfoSub: Subscription;
    private datamodeSub: Subscription;
    private ServiceStudentCertifSub: Subscription;
    private epalUserDataSub: Subscription;
    private datamode$: BehaviorSubject<IDataModeRecords>;
    private modalTitle: BehaviorSubject<string>;
    private modalText: BehaviorSubject<string>;
    private modalHeader: BehaviorSubject<string>;
    public isModalShown: BehaviorSubject<boolean>;
    private showLoader: BehaviorSubject<boolean>;
    //private ServiceStudentCertif$: BehaviorSubject<any>;
    private currentUrl: string;
    private cu_name: string;
    private cu_surname: string;
    private cu_fathername: string;
    private cu_mothername: string;
    private disclaimer_checked: number;
    private hasright: number;
    private app_update: BehaviorSubject<boolean>;
    private appId: BehaviorSubject<string>;
    private previousClass: BehaviorSubject<string>;
    private previousSector: BehaviorSubject<string>;
    private previousCourse: BehaviorSubject<string>;
    private previousSchools: BehaviorSubject<string>;
    private wsIdentSub: Subscription;
    private wsEnabled: BehaviorSubject<number>;
    private limitSchoolYear: string;

    constructor(
        private _hds: HelperDataService,
        private _csa: SectorCoursesActions,
        private _sfa: SectorFieldsActions,
        private _rsa: RegionSchoolsActions,
        private _eca: EpalClassesActions,
        private _sdfa: StudentDataFieldsActions,
        private _cfa: DataModeActions,
        private _ngRedux: NgRedux<IAppState>,
        private router: Router,
        private http: Http
    ) {

        this.epalclasses$ = new BehaviorSubject(EPALCLASSES_INITIAL_STATE);
        this.studentDataFields$ = new BehaviorSubject(STUDENT_DATA_FIELDS_INITIAL_STATE);
        this.loginInfo$ = new BehaviorSubject(LOGININFO_INITIAL_STATE);

        this.modalTitle = new BehaviorSubject("");
        this.modalText = new BehaviorSubject("");
        this.modalHeader = new BehaviorSubject("");
        this.isModalShown = new BehaviorSubject(false);
        this.showLoader = new BehaviorSubject(false);
        this.app_update = new BehaviorSubject(false);
        this.appId = new BehaviorSubject("");

        this.previousClass = new BehaviorSubject("");
        this.previousSector = new BehaviorSubject("");
        this.previousCourse = new BehaviorSubject("");
        this.previousSchools = new BehaviorSubject("");
        this.wsEnabled = new BehaviorSubject(-1);

        //this.sectorSelectedName = null;
        //this.courseSelectedName = null;
        this.sectorSelected = null;
        this.courseSelected = null;
        this.hasright = 1;
        this.previousSchools.next("");
        //this.ServiceStudentCertif$ = new BehaviorSubject([{}]);
        this.limitSchoolYear = "2013-2014";

        this.wsIdentSub = this._hds.isWS_ident_enabled().subscribe(z => {
            this.wsEnabled.next(Number(z.res)) ;
       });
    };

    ngOnInit() {

        (<any>$("#studentFormSentNotice")).appendTo("body");
        window.scrollTo(0, 0);

        this.epalUserDataSub = this._hds.getApplicantUserData().subscribe(x => {
            if ( Number(x.numAppSelf) > 0 && Number(x.numAppChildren) >= Number(x.numChildren))
              this.hasright = 0;
        });

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
                        //if ( Number(loginInfoObj.numapp_self) > 0 && Number(loginInfoObj.numapp_children) >= Number(loginInfoObj.numchildren) )
                        //  this.hasright = 0;

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
                                this.previousClass.next(datamode.get("currentclass"));
                                this.previousSector.next(datamode.get("sector_id"));
                                this.previousCourse.next(datamode.get("course_id"));
                                for (let i=0; i < datamode.get("epal_choice").length; i++)
                                  this.previousSchools.next(datamode.get("epal_choice")[i].id + "," + this.previousSchools.getValue());
                            }
                            return datamode;
                        }, {});
                    } else {

                    }
                }, error => { console.log("error selecting datamode"); });


        this.epalclassesSub = this._ngRedux.select("epalclasses")
            .map(epalClasses => <IEpalClassRecords>epalClasses)
            .subscribe(ecs => {
            ecs.reduce(({ }, epalclass) => {
                this.classSelected = epalclass.get("name");
                return epalclass;
            }, {});
            this.epalclasses$.next(ecs);
        }, error => { console.log("error selecting epalclasses"); });

        this.studentDataFieldsSub = this._ngRedux.select("studentDataFields")
            .subscribe(studentDataFields => {
                this.studentDataFields$.next(<IStudentDataFieldRecords>studentDataFields);
            }, error => { console.log("error selecting studentDataFields"); });

        this.regionsSub = this._ngRedux.select("regions").
            subscribe(regions => {
                let rgns = <IRegionRecords>regions;
                let prevSelected: Array<number> = new Array();
                rgns.reduce((prevRgn, rgn) => {
                    rgn.epals.reduce((prevSchool, school) => {
                        if (school.selected === true) {
                            prevSelected = this.epalSelected$.getValue();
                            prevSelected[prevSelected.length] = <number>parseInt(school.epal_id);
                            this.epalSelected$.next(prevSelected);
                            this.epalSelectedOrder.push(school.order_id);

                            this.epalSelectedName.push(school.epal_name);
                            this.epalSelectedId.push(school.epal_id);
                        }
                        return school;
                    }, {});
                    return rgn;
                }, {});
                //                    this.regions$.next(<IRegionRecords>regions);
            },
            error => {
                console.log("Error Selecting Regions");
            }
            );


        this.sectorsSub = this._ngRedux.select("sectors")
            .map(sectors => <ISectorRecords>sectors)
            .subscribe(scs => {
                scs.reduce((prevSector, sector) => {
                    sector.get("courses").reduce((prevCourse, course) => {
                        if (course.get("selected") === true) {
                            this.courseSelected = course.get("course_id");
                            //this.courseSelectedName = course.get("course_name");
                        }
                        return course;
                    }, {});
                    return sector;
                }, {});
            });

        this.sectorFieldsSub = this._ngRedux.select("sectorFields")
            .map(sectorFields => <ISectorFieldRecords>sectorFields)
            .subscribe(sfds => {
                sfds.reduce(({ }, sectorField) => {
                    if (sectorField.selected === true) {
                        this.sectorSelected = sectorField.id;
                        //this.sectorSelectedName = sectorField.name;
                    }
                    return sectorField;
                }, {});
            });

    };

    ngOnDestroy() {
        (<any>$("#studentFormSentNotice")).remove();
        if (this.studentDataFieldsSub)
            this.studentDataFieldsSub.unsubscribe();
        if (this.regionsSub)
            this.regionsSub.unsubscribe();
        if (this.sectorsSub)
            this.sectorsSub.unsubscribe();
        if (this.sectorFieldsSub)
            this.sectorFieldsSub.unsubscribe();
        if (this.epalclassesSub)
            this.epalclassesSub.unsubscribe();
        if (this.loginInfoSub)
            this.loginInfoSub.unsubscribe();
        if (this.datamodeSub)
            this.datamodeSub.unsubscribe();
        if (this.epalUserDataSub)
            this.epalUserDataSub.unsubscribe();
        if (this.wsIdentSub)
            this.wsIdentSub.unsubscribe();
        if (this.ServiceStudentCertifSub)
            this.ServiceStudentCertifSub.unsubscribe();
    }

    submitNow(newapp) {

        //έλεγχος αν πρέπει να γίνει έλεγχος πληρότητας
        let nonCheckOccupancy = "$";
        if (newapp === false) {
          if (this.classSelected === this.previousClass.getValue() && this.sectorSelected === this.previousSector.getValue() && this.courseSelected === this.previousCourse.getValue()) {
            for (let i=0; i < this.epalSelectedId.length; i++) {
                if (this.previousSchools.getValue().indexOf(this.epalSelectedId[i]) !== -1) {
                  nonCheckOccupancy += this.epalSelectedId[i] + "$";
                }
            }
          }
        }

        if (this.studentDataFields$.getValue().size === 0 || this.epalSelected$.getValue().length === 0 || this.epalclasses$.getValue().size === 0 || this.loginInfo$.getValue().size === 0)
            return;

        let aitisiObj: Array<any> = [];
        let epalObj: Array<StudentEpalChosen> = [];
        let std = this.studentDataFields$.getValue().get(0);

        aitisiObj[0] = <any>{};
        aitisiObj[0].name = std.get("name");
        aitisiObj[0].studentsurname = std.get("studentsurname");
        aitisiObj[0].fatherfirstname = std.get("fatherfirstname");
        aitisiObj[0].motherfirstname = std.get("motherfirstname");
        aitisiObj[0].studentbirthdate = std.get("studentbirthdate");
        aitisiObj[0].lastschool_schoolyear = std.get("lastschool_schoolyear");
        aitisiObj[0].lastschool_registrynumber = std.get("lastschool_schoolname").registry_no;
        aitisiObj[0].lastschool_schoolname = std.get("lastschool_schoolname").name;
        aitisiObj[0].lastschool_unittypeid = std.get("lastschool_schoolname").unit_type_id;
        //aitisiObj[0].lastschool_class = std.get("lastschool_class");
        aitisiObj[0].lastschool_class = null;
        aitisiObj[0].relationtostudent = std.get("relationtostudent");
        aitisiObj[0].telnum = std.get("telnum");
        aitisiObj[0].cu_name = this.cu_name;
        aitisiObj[0].cu_surname = this.cu_surname;
        aitisiObj[0].cu_fathername = this.cu_fathername;
        aitisiObj[0].cu_mothername = this.cu_mothername;
        aitisiObj[0].disclaimer_checked = this.disclaimer_checked;
        aitisiObj[0].hasright = this.hasright;
        aitisiObj[0].currentclass = this.classSelected;

        aitisiObj[0].am = null;
        if (aitisiObj[0].lastschool_schoolyear >=   this.limitSchoolYear)
          aitisiObj[0].am =  std.get("am");
        else {
          aitisiObj[0].regionaddress = std.get("regionaddress");
          aitisiObj[0].regionarea = std.get("regionarea");
          aitisiObj[0].regiontk = std.get("regiontk");
        }
        aitisiObj[0].section_name = null;

        let epalSelected = this.epalSelected$.getValue();
        for (let i = 0; i < epalSelected.length; i++) {
            epalObj[i] = new StudentEpalChosen(null, epalSelected[i], this.epalSelectedOrder[i]);
        }
        aitisiObj["1"] = epalObj;

        if (aitisiObj[0]["currentclass"] === "2") {
            aitisiObj["2"] = new StudentSectorChosen(null, this.sectorSelected);
        } else if (aitisiObj[0]["currentclass"] === "3" || aitisiObj[0]["currentclass"] === "4") {
            aitisiObj["2"] = new StudentCourseChosen(null, this.courseSelected);
        }

        //κλήση myschool web service
        if (this.wsEnabled.getValue() === 1 && aitisiObj[0].lastschool_schoolyear >=   this.limitSchoolYear)  {
              //this.ServiceStudentCertifSub = this._hds.getServiceStudentPromotion('24','null','null','null','null','04-01-1997','0540961','777')
              this.ServiceStudentCertifSub = this._hds.getServiceStudentPromotion('24','null','null','null','null',
                      aitisiObj[0].studentbirthdate + "T00:00:00", aitisiObj[0].lastschool_registrynumber, aitisiObj[0].am)
                .subscribe(data => {
                    if (typeof data.data["id"] !== "undefined")  {
                      aitisiObj[0].studentId = data.data["id"];
                      //aitisiObj[0].websrv_cu_name = data.data["custodianFirstName"];
                      aitisiObj[0].websrv_cu_surname = data.data["custodianLastName"];
                      //aitisiObj[0].websrv_studentbirthdate = data.birthDate;
                      aitisiObj[0].regionaddress = data.data["addressStreet"];
                      aitisiObj[0].regiontk = data.data["addressPostCode"];
                      aitisiObj[0].regionarea = data.data["addressArea"];
                      aitisiObj[0].lastschool_class = data.data["levelName"];
                      aitisiObj[0].section_name = data.data["sectionName"];
                    }
                    else {
                      let mTitle = "Αποτυχία Ταυτοποίησης Μαθητή στο Πληροφοριακό Σύστημα myschool";
                      let mText = "Δεν βρέθηκε μαθητής στο ΠΣ myschool με τα στοιχεία που δώσατε. " +
                        "Παρακαλώ προσπαθήστε ξανά αφού πρώτα ελέγξετε την ορθότητα των ακόλουθων στοιχείων: Αριθμός Μητρώου, Σχολείο τελευτάιας φοίτησης, Ημερομηνία Γέννησης. " +
                        "Σε περίπτωση που συνεχίσετε να αντιμετωπίζετε προβλήματα επικοινωνήστε με την ομάδα υποστήριξης. ";
                      let mHeader = "modal-header-danger";
                      this.modalTitle.next(mTitle);
                      this.modalText.next(mText);
                      this.modalHeader.next(mHeader);
                      this.showModal();
                      (<any>$(".loading")).remove();

                      return;

                    }

                    if (aitisiObj[0].websrv_cu_surname.replace(/ |-/g, "") !== aitisiObj[0].cu_surname.replace(/ |-/g, "")) {
                      let mTitle = "Αποτυχία Ταυτοποίησης Κηδεμόνα";
                      let mText = "Ο Κηδεμόνας που έχει δηλωθεί στο ΠΣ myschool έχει ΔΙΑΦΟΡΕΤΙΚΑ στοιχεία από το χρήστη που έχει κάνει είσοδο σε αυτό το σύστημα μέσω των κωδικών του taxisnet. " +
                        "Παρακαλώ επικοινωνήστε με το σχολείο σας για να επιβεβαιώσετε ότι το ονοματεπώνυμο του κηδεμόνα έχει καθοριστεί σωστά στο ΠΣ myschοol. " +
                        "Σε περίπτωση που συνεχίσετε να αντιμετωπίζετε προβλήματα επικοινωνήστε με την ομάδα υποστήριξης. ";
                      let mHeader = "modal-header-danger";
                      this.modalTitle.next(mTitle);
                      this.modalText.next(mText);
                      this.modalHeader.next(mHeader);
                      this.showModal();
                      (<any>$(".loading")).remove();

                      return;
                    }
                    //console.log(aitisiObj[0]);
                    this.submitRecord(newapp, nonCheckOccupancy, aitisiObj);
                },
                error => {
                    console.log("Error Getting Courses");
                });
        }

        else  {
          this.submitRecord(newapp, nonCheckOccupancy, aitisiObj);
        }

    }


    submitRecord(newapp, nonCheckOccupancy, record) {
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
            1023: "Τάξη τελευταίας φοίτησης",
            1024: "Μοναδικός αριθμός μαθητή για έτος μικρότερο του σχολικού έτους αναφοράς",
            1025: "Μη Μοναδικός αριθμός μαθητή για έτος μεγαλύτεο ίσο του σχολικού έτους αναφοράς"
        };
        let authTokenPost = this.authToken + ":" + this.authToken;

        let headers = new Headers({
            "Authorization": "Basic " + btoa(authTokenPost),
            "Accept": "*/*",
            "Access-Control-Allow-Credentials": "true",
            "Content-Type": "application/json",
        });

        let options = new RequestOptions({ headers: headers, method: "post", withCredentials: true });
        let connectionString = `${AppSettings.API_ENDPOINT}/epal/appsubmit`;
        if (!newapp)
          //connectionString = `${AppSettings.API_ENDPOINT}/epal/appupdate/` + this.appId.getValue();
          connectionString = `${AppSettings.API_ENDPOINT}/epal/appupdate/` + this.appId.getValue() + '/' + nonCheckOccupancy;
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
                        this._eca.initEpalClasses();
                        this._sfa.initSectorFields();
                        this._rsa.initRegionSchools();
                        this._csa.initSectorCourses();
                        this._sdfa.initStudentDataFields();
                        break;
                    case 997:
                        mTitle = "Αποτυχία Υποβολής Δήλωσης Προτίμησης";
                        mText = "Έχετε ήδη υποβάλλει το σύνολο των αιτήσεων που δικαιούστε να κάνετε.";
                        mHeader = "modal-header-danger";
                        break;
                    case 998:
                        mTitle = "Αποτυχία Υποβολής Δήλωσης Προτίμησης";
                        mText = "Δεν έχετε επιλέξει ειδικότητα";
                        mHeader = "modal-header-danger";
                        break;
                    case 999:
                        mTitle = "Αποτυχία Υποβολής Δήλωσης Προτίμησης";
                        mText = "Δεν έχετε επιλέξει τομέα";
                        mHeader = "modal-header-danger";
                        break;
                    case 1000:
                        mTitle = "Αποτυχία Υποβολής Δήλωσης Προτίμησης";
                        mText = "Δεν έχετε επιλέξει σχολεία";
                        mHeader = "modal-header-danger";
                        break;
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
                    case 1024:
                        mTitle = "Αποτυχία Υποβολής Δήλωσης Προτίμησης";
                        mText = "Παρακαλούμε ελέγξτε τα στοιχεία που υποβάλλετε. Ύπαρξη επιστρεφόμενου μοναδικού αριθμόυ μαθητή για σχολικό έτος < " + this.limitSchoolYear + ".";
                        mHeader = "modal-header-danger";
                        break;
                    case 1025:
                        mTitle = "Αποτυχία Υποβολής Δήλωσης Προτίμησης";
                        mText = "Παρακαλούμε ελέγξτε τα στοιχεία που υποβάλλετε. Μη ύπαρξη επιστρεφόμενου μοναδικού αριθμόυ μαθητή για σχολικό έτος >= " + this.limitSchoolYear + ".";
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
                        mText = "Παρακαλώ ελέγξτε τη φόρμα σας και προσπαθήστε ξανά. Φαίνεται να έχετε ήδη κάνει δήλωση για τον ίδιο μαθητή. ";
                        mHeader = "modal-header-danger";
                        break;
                    case 9001:
                        //let schoolName = success.school_name;
                        mTitle = "Αποτυχία Υποβολής Δήλωσης Προτίμησης";
                        mText =  "Το σχολείο " + success.school_name + " που επιλέξατε, δεν έχει αυτή τη στιγμή διαθέσιμες θέσεις εγγραφής. Για να ολοκληρώσετε την υποβολή της αίτησης θα πρέπει να τροποποιήσετε τις επιλογές σχολικών μονάδων που κάνατε.";
                        mHeader = "modal-header-danger";
                        break;
                    case 9003:
                        mTitle = "Αποτυχία Υποβολής Δήλωσης Προτίμησης";
                        mText =  "Το τμήμα στο σχολείο " + success.school_name + " που επιλέξατε, είναι ΜΗ ΕΓΚΕΚΡΙΜΕΝΟ. Για να ολοκληρώσετε την υποβολή της αίτησης θα πρέπει να τροποποιήσετε τις επιλογές σχολικών μονάδων που κάνατε.";
                        mHeader = "modal-header-danger";
                        break;
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
        this.router.navigate(["/student-application-form-main"]);
    }

}
