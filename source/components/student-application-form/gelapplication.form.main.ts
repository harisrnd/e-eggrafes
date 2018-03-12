import { NgRedux } from "@angular-redux/store";
import { Injectable } from "@angular/core";
import { Component, OnInit } from "@angular/core";
import { FormBuilder, FormControl, FormGroup, Validators } from "@angular/forms";
import { Http, Headers, RequestOptions } from "@angular/http";
import { Router } from "@angular/router";
import { IMyDpOptions } from "mydatepicker";
import { BehaviorSubject, Observable, Subscription } from "rxjs/Rx";
import { IDataModeRecords } from "../../store/datamode/datamode.types";
import { DataModeActions } from "../../actions/datamode.actions";
import { GelStudentDataFieldsActions } from "../../actions/gelstudentdatafields.actions";
import { LOGININFO_INITIAL_STATE } from "../../store/logininfo/logininfo.initial-state";
import { ILoginInfoRecords } from "../../store/logininfo/logininfo.types";
import { IAppState } from "../../store/store";
import { GELSTUDENT_DATA_FIELDS_INITIAL_STATE } from "../../store/gelstudentdatafields/gelstudentdatafields.initial-state";
import { IGelStudentDataFieldRecords } from "../../store/gelstudentdatafields/gelstudentdatafields.types";
import { GelClassesActions } from "../../actions/gelclasses.actions";
import { IGelClass, IGelClassRecord, IGelClassRecords } from "../../store/gelclasses/gelclasses.types";
import { HelperDataService } from "../../services/helper-data-service";
import { AppSettings } from "../../app.settings";
import {
    FIRST_SCHOOL_YEAR,
    VALID_ADDRESS_PATTERN,
    VALID_ADDRESSTK_PATTERN,
    VALID_NAMES_PATTERN,
    VALID_TELEPHONE_PATTERN,
    VALID_UCASE_NAMES_PATTERN,
} from "../../constants";

@Component({
    selector: "gelapplication-form-main",
    templateUrl: "./gelapplication.form.main.html"
})

@Injectable() export default class GelStudentApplicationMain implements OnInit {

    private loginInfo$: BehaviorSubject<ILoginInfoRecords>;
    private studentDataFields$: BehaviorSubject<IGelStudentDataFieldRecords>;
    //private datamode$: BehaviorSubject<IDataModeRecords>;

    private studentDataFieldsSub: Subscription;
    private loginInfoSub: Subscription;
    private criteriaSub: Subscription;
    private datamodeSub: Subscription;
    private gelUserDataSub: Subscription;
    private gelclassesSub: Subscription;

    private studentDataGroup: FormGroup;
    private studentCriteriaGroup: FormGroup;

    private modalTitle: BehaviorSubject<string>;
    private modalText: BehaviorSubject<string>;
    private modalHeader: BehaviorSubject<string>;
    private schoolyears: string[];
    private appId: BehaviorSubject<string>;
    private appUpdate: BehaviorSubject<boolean>;
    private dataEdit: BehaviorSubject<boolean>;
    private lastSchName: BehaviorSubject<string>;
    private previousClass: BehaviorSubject<string>;
    private previousSector: BehaviorSubject<string>;
    private previousCourse: BehaviorSubject<string>;
    private previousSchools: BehaviorSubject<string>;
    private reltostud:  BehaviorSubject<string>;
    private numAppSelf: BehaviorSubject<number>;
    private numAppChildren: BehaviorSubject<number>;
    private numChildren: BehaviorSubject<number>;
    //private gelUserData$: BehaviorSubject<any>;
    private activeClassId = -1;
    private wsIdentSub: Subscription;
    private wsEnabled:  BehaviorSubject<number>;

    private myDatePickerOptions: IMyDpOptions = {
        // other options...
        sunHighlight: false,
        editableDateField: false,
        dateFormat: "dd/mm/yyyy",
    };

    /*
    private observableSource = (keyword: any): Observable<any[]> => {
        let url: string = "https://mm.sch.gr/api/units?name=" + keyword;
        if (keyword) {
            return this.http.get(url)
                .map(res => {
                    let json = res.json();
                    let retArr = <any>Array();
                    for (let i = 0; i < json.data.length; i++) {
                        retArr[i] = {};
                        retArr[i].registry_no = json.data[i].registry_no;
                        retArr[i].name = json.data[i].name;
                        retArr[i].unit_type_id = json.data[i].unit_type_id;
                    }
                    return retArr;
                });
        } else {
            return Observable.of([]);
        }
    };
    */

    private observableSource = (keyword: any): Observable<any[]> => {
        let headers = new Headers({
            "Content-Type": "application/json",
        });
        this.loginInfo$.getValue().forEach(loginInfoToken => {
            headers.append("Authorization", "Basic " + btoa( loginInfoToken.auth_token + ":" +  loginInfoToken.auth_token));
       });
       let options = new RequestOptions({ headers: headers });

       let url: string = `${AppSettings.API_ENDPOINT}/deploysystem/getschoollist/` + keyword ;
        if (keyword) {
            return this.http.get(url, options)
                .map(res => {
                    let json = res.json();
                    let retArr = <any>Array();
                    for (let i = 0; i < json.length; i++) {
                        retArr[i] = {};
                        retArr[i].registry_no = json[i].registry_no;
                        retArr[i].name = json[i].name;
                        retArr[i].unit_type_id = json[i].unit_type_id;
                    }
                    return retArr;
                });
        } else {
            return Observable.of([]);
        }
    };

    constructor(private fb: FormBuilder,
        private _sdfa: GelStudentDataFieldsActions,
        private _cfb: GelClassesActions,
        private hds: HelperDataService,
        //private _cfa: DataModeActions,
        private _ngRedux: NgRedux<IAppState>,
        private router: Router,
        private http: Http) {

        this.populateSchoolyears();
        this.modalTitle = new BehaviorSubject("");
        this.modalText = new BehaviorSubject("");
        this.modalHeader = new BehaviorSubject("");
        this.loginInfo$ = new BehaviorSubject(LOGININFO_INITIAL_STATE);
        this.appId = new BehaviorSubject("");
        this.appUpdate = new BehaviorSubject(false);
        this.dataEdit = new BehaviorSubject(false);
        this.lastSchName = new BehaviorSubject("");
        this.previousClass = new BehaviorSubject("");
        this.previousSector = new BehaviorSubject("");
        this.previousCourse = new BehaviorSubject("");
        this.previousSchools = new BehaviorSubject("");
        this.reltostud = new BehaviorSubject("");
        this.numAppSelf = new BehaviorSubject(0);
        this.numAppChildren = new BehaviorSubject(0);
        this.numChildren = new BehaviorSubject(0);
        this.wsEnabled = new BehaviorSubject(-1);

        //this.gelUserData$ = new BehaviorSubject(<any>{ userEmail: "", userName: "", userSurname: "", userFathername: "", userMothername: "" ,
        //                                                representRole: "", numAppSelf: 0, numAppChildren: 0, numChildren: 0 });

        this.studentDataFields$ = new BehaviorSubject(GELSTUDENT_DATA_FIELDS_INITIAL_STATE);
        this.studentDataGroup = this.fb.group({
            name: ["", [Validators.pattern(VALID_UCASE_NAMES_PATTERN), Validators.required]],
            studentsurname: ["", [Validators.pattern(VALID_UCASE_NAMES_PATTERN), Validators.required]],
            studentbirthdate: ["", [Validators.required]],
            fatherfirstname: ["", [Validators.pattern(VALID_UCASE_NAMES_PATTERN), Validators.required]],
            motherfirstname: ["", [Validators.pattern(VALID_UCASE_NAMES_PATTERN), Validators.required]],
            regionaddress: ["", [Validators.pattern(VALID_ADDRESS_PATTERN), Validators.required]],
            regiontk: ["", [Validators.pattern(VALID_ADDRESSTK_PATTERN), Validators.required]],
            regionarea: ["", [Validators.pattern(VALID_NAMES_PATTERN), Validators.required]],
            relationtostudent: ["", this.checkChoice],
            telnum: ["", [Validators.pattern(VALID_TELEPHONE_PATTERN), Validators.required]],
            lastschool_schoolname: ["", [Validators.required]],
            lastschool_schoolyear: ["", this.checkChoice],
            //lastschool_class: ["", this.checkChoice],
            am: ["", [Validators.required]],
        });

        this.wsIdentSub = this.hds.isWS_ident_enabled().subscribe(z => {
            this.wsEnabled.next(Number(z.res)) ;
            console.log(this.wsEnabled.getValue());
       });

    };

    ngOnInit() {
        (<any>$("#applicationFormNotice")).appendTo("body");
        window.scrollTo(0, 0);

        //this._cfb.getClassesList(false);
        this.gelclassesSub = this._ngRedux.select("gelclasses")
            .map(gelclasses => <IGelClassRecords>gelclasses)
            .subscribe(ecs => {
                if (ecs.size > 0) {
                     ecs.reduce(({}, gelclass) => {
                        if (gelclass.get("selected")===true ){
                            this.activeClassId = gelclass.get("id");
                        }
                        return gelclass;
                    }, {});
                }
            }, error => { console.log("error selecting gelclasses"); });

        this.gelUserDataSub = this.hds.getApplicantUserData().subscribe(x => {
            //this.gelUserData$.next(x);
            this.numAppSelf.next(Number(x.numAppSelf));
            this.numAppChildren.next(Number(x.numAppChildren));
            this.numChildren.next(Number(x.numChildren));

            this.studentDataFieldsSub = this._ngRedux.select("gelstudentDataFields")
                .subscribe(studentDataFields => {
                    let sdfds = <IGelStudentDataFieldRecords>studentDataFields;
                    if (sdfds.size > 0) {
                        sdfds.reduce(({}, studentDataField) => {
                            //if (this.appUpdate.getValue() &&  !this.dataEdit.getValue())
                            this.lastSchName.next((studentDataField.get("lastschool_schoolname")).name);
                            if (typeof this.lastSchName.getValue() === "undefined" )
                              this.lastSchName.next("");

                              if (this.wsEnabled.getValue()===0){
                                this.studentDataGroup.controls["name"].setValue(studentDataField.get("name"));
                                this.studentDataGroup.controls["studentsurname"].setValue(studentDataField.get("studentsurname"));
                                this.studentDataGroup.controls["fatherfirstname"].setValue(studentDataField.get("fatherfirstname"));
                                this.studentDataGroup.controls["motherfirstname"].setValue(studentDataField.get("motherfirstname"));
                                this.studentDataGroup.controls["regionaddress"].setValue(studentDataField.get("regionaddress"));
                                this.studentDataGroup.controls["regiontk"].setValue(studentDataField.get("regiontk"));
                                this.studentDataGroup.controls["regionarea"].setValue(studentDataField.get("regionarea"));
                                this.studentDataGroup.controls["lastschool_schoolname"].setValue(studentDataField.get("lastschool_schoolname"));
                                this.studentDataGroup.controls["lastschool_schoolyear"].setValue(studentDataField.get("lastschool_schoolyear"));
                                this.studentDataGroup.controls["relationtostudent"].setValue(studentDataField.get("relationtostudent"));
                                this.studentDataGroup.controls["telnum"].setValue(studentDataField.get("telnum"));
                                this.studentDataGroup.controls["studentbirthdate"].setValue(this.populateDate(studentDataField.get("studentbirthdate")));

                                this.studentDataGroup.controls["am"].setValidators(null);
                                this.studentDataGroup.controls["am"].updateValueAndValidity();
                            }
                            else{
                                if (studentDataField.get("lastschool_schoolyear")>="2013-2014"){
                                    this.studentDataGroup.controls["am"].setValue(studentDataField.get("am"));
                                    this.studentDataGroup.controls["name"].setValue(studentDataField.get("name"));
                                    this.studentDataGroup.controls["studentsurname"].setValue(studentDataField.get("studentsurname"));
                                    this.studentDataGroup.controls["fatherfirstname"].setValue(studentDataField.get("fatherfirstname"));
                                    this.studentDataGroup.controls["motherfirstname"].setValue(studentDataField.get("motherfirstname"));;
                                    this.studentDataGroup.controls["lastschool_schoolname"].setValue(studentDataField.get("lastschool_schoolname"));
                                    this.studentDataGroup.controls["lastschool_schoolyear"].setValue(studentDataField.get("lastschool_schoolyear"));
                                    this.studentDataGroup.controls["relationtostudent"].setValue(studentDataField.get("relationtostudent"));
                                    this.studentDataGroup.controls["telnum"].setValue(studentDataField.get("telnum"));
                                    this.studentDataGroup.controls["studentbirthdate"].setValue(this.populateDate(studentDataField.get("studentbirthdate")));

                                    this.studentDataGroup.controls["regionaddress"].setValidators(null);
                                    this.studentDataGroup.controls["regiontk"].setValidators(null);
                                    this.studentDataGroup.controls["regionarea"].setValidators(null);
                                    this.studentDataGroup.controls["regionaddress"].updateValueAndValidity();
                                    this.studentDataGroup.controls["regiontk"].updateValueAndValidity();
                                    this.studentDataGroup.controls["regionarea"].updateValueAndValidity();
                                }
                                else if (studentDataField.get("lastschool_schoolyear")<"2013-2014" && studentDataField.get("lastschool_schoolyear")!=""){
                                    this.studentDataGroup.controls["name"].setValue(studentDataField.get("name"));
                                    this.studentDataGroup.controls["studentsurname"].setValue(studentDataField.get("studentsurname"));
                                    this.studentDataGroup.controls["fatherfirstname"].setValue(studentDataField.get("fatherfirstname"));
                                    this.studentDataGroup.controls["motherfirstname"].setValue(studentDataField.get("motherfirstname"));
                                    this.studentDataGroup.controls["regionaddress"].setValue(studentDataField.get("regionaddress"));
                                    this.studentDataGroup.controls["regiontk"].setValue(studentDataField.get("regiontk"));
                                    this.studentDataGroup.controls["regionarea"].setValue(studentDataField.get("regionarea"));
                                    this.studentDataGroup.controls["lastschool_schoolname"].setValue(studentDataField.get("lastschool_schoolname"));
                                    this.studentDataGroup.controls["lastschool_schoolyear"].setValue(studentDataField.get("lastschool_schoolyear"));
                                    this.studentDataGroup.controls["relationtostudent"].setValue(studentDataField.get("relationtostudent"));
                                    this.studentDataGroup.controls["telnum"].setValue(studentDataField.get("telnum"));
                                    this.studentDataGroup.controls["studentbirthdate"].setValue(this.populateDate(studentDataField.get("studentbirthdate")));

                                    this.studentDataGroup.controls["am"].setValidators(null);
                                    this.studentDataGroup.controls["am"].updateValueAndValidity();
                                }

                            }

                            //λύση προβλήματος πεδίου "Αίτηση από" στο edit app
                            if (this.appUpdate.getValue() === true) {
                                if (studentDataField.get("relationtostudent") === 'Γονέας/Κηδεμόνας')
                                  this.numAppChildren.next(this.numAppChildren.getValue() -1) ;
                                else if (studentDataField.get("relationtostudent") === 'Μαθητής')
                                  this.numAppSelf.next(this.numAppSelf.getValue() - 1);

                            }

                            return studentDataField;
                        }, {});
                    }
                    this.studentDataFields$.next(sdfds);
                }, error => { console.log("error selecting studentDataFields"); });


        });

         this.loginInfoSub = this._ngRedux.select("loginInfo")
            .map(loginInfo => <ILoginInfoRecords>loginInfo)
            .subscribe(linfo => {
                this.loginInfo$.next(linfo);
          }, error => { console.log("error selecting loginInfo"); });

         this.datamodeSub = this._ngRedux.select("datamode")
              .map(datamode => <IDataModeRecords>datamode)
              .subscribe(ecs => {
                  if (ecs.size > 0) {
                      ecs.reduce(({}, datamode,i) => {
                          this.appUpdate.next(datamode.get("app_update"));
                          return datamode;
                      }, {});
                  }
              }, error => { console.log("error selecting datamode"); });

    };

    ngOnDestroy() {
        (<any>$("#applicationFormNotice")).remove();
        if (this.studentDataFieldsSub) this.studentDataFieldsSub.unsubscribe();
        if (this.datamodeSub) this.datamodeSub.unsubscribe();
        if (this.gelUserDataSub) this.gelUserDataSub.unsubscribe();
    }

    navigateBack() {
        this._sdfa.saveGelStudentDataFields([this.studentDataGroup.value]);

        if (this.activeClassId == 1 || this.activeClassId == 3 || this.activeClassId == 4)
          this.router.navigate(["/electivecourse-fields-select"]);
        else if (this.activeClassId == 2 || this.activeClassId == 6 || this.activeClassId == 7)
          this.router.navigate(["/orientation-group-select"]);
        else if (this.activeClassId == 5) {
          //this._cfb.resetGelClassesSelected();
          this.router.navigate(["/gel-class-select"]);
        }
    }

    submitSelected() {
        let invalidFlag = 0;
        if (this.studentDataGroup.invalid || (invalidFlag = this.invalidFormData()) > 0) {
            this.modalHeader.next("modal-header-danger");
            this.modalTitle.next("Η δήλωση προτίμησης δεν είναι πλήρης");
            if (invalidFlag === 1 || invalidFlag === 2)
                this.modalText.next("Πρέπει να συμπληρώσετε όλα τα πεδία που συνοδεύονται από (*). Η ημερομηνία γέννησης του μαθητή δεν είναι επιτρεπόμενη για μαθητή ΕΠΑΛ.");
            else if (invalidFlag === 3)
                this.modalText.next("Πρέπει να συμπληρώσετε όλα τα πεδία που συνοδεύονται από (*). Το σχολείο τελευταίας φοίτησης πρέπει να αναζητηθεί και να επιλεχθεί από τα αποτελέσματα της αναζήτησης.");
            else if (invalidFlag === 4)
                this.modalText.next("Πρέπει να συμπληρώσετε όλα τα πεδία που συνοδεύονται από (*). Το τηλέφωνο επικοινωνίας πρέπει να είναι σταθερό τηλέφωνο και να αποτελείται από 10 ψηφία.");
            else
                this.modalText.next("Πρέπει να συμπληρώσετε όλα τα πεδία που συνοδεύονται από (*)");

            this.showModal();
        } else {
            this._sdfa.saveGelStudentDataFields([this.studentDataGroup.value]);

          this.router.navigate(["/gel-application-submit"]);

        }
    }

    private invalidFormData(): number {

        let d = this.studentDataGroup.controls["studentbirthdate"].value;
        if (!d || !d.date || !d.date.year)
            return 1;
        else if ((new Date().getFullYear()) - d.date.year < 14)
            return 2;
        if (//this.appmode !== "edit" &&
            !this.studentDataGroup.controls["lastschool_schoolname"].value.registry_no &&
            this.studentDataGroup.controls["lastschool_schoolname"].value.unit_type_id !== 38)
            return 3;
        else if (this.studentDataGroup.controls["lastschool_schoolname"].value.unit_type_id === 38)
            this.studentDataGroup.controls["lastschool_schoolname"].value.registry_no = "0000000";
        if (this.studentDataGroup.controls["telnum"].value.length !== 10)
            return 4;

        return 0;
    }

    checkcriteria(cb, mutual_disabled) {
        if (mutual_disabled !== "-1" && cb.checked === true) {
            let mutual_ids = mutual_disabled.split(",");
            for (let i = 0; i < mutual_ids.length; i++) {
                this.studentCriteriaGroup.controls["formArray"]["controls"][mutual_ids[i] - 1].setValue(false);
            }

        }
    }

    checkChoice(c: FormControl) {
        return (c.value === "") ? { status: true } : null;
    }

    populateDate(d) {
        if (d && d.length > 0) {
            return {
                date: {
                    year: d ? parseInt(d.substr(0, 4)) : 0,
                    month: d ? parseInt(d.substr(5, 7)) : 0,
                    day: d ? parseInt(d.substr(8, 10)) : 0
                }
            };
        } else {
            return {
                date: null
            };
        }
    }

    private populateSchoolyears(): void {
        let endYear = new Date().getFullYear();
        this.schoolyears = new Array();
        for (let i = endYear; i > FIRST_SCHOOL_YEAR; i--) {
            this.schoolyears.push((i - 1) + "-" + i);
        }
    };

    setDate() {
        let date = new Date();
        return {
            date: {
                year: date.getFullYear() - 14,
                month: date.getMonth() + 1,
                day: date.getDate()
            }
        };
    }

    clearDate() {
        return null;
    }

    public showModal(): void {
        (<any>$("#applicationFormNotice")).modal("show");
    }

    public hideModal(): void {
        (<any>$("#applicationFormNotice")).modal("hide");
    }

    lastSchoolListFormatter(data: any): string {
        return data.name;
    };

    lastSchoolValueFormatter(data: any): string {
        return data.name;
    };

    lastSchoolValueChanged(e: any): void {
    };

    public schoolyearselected(){

        if (this.wsEnabled.getValue()===1){

            if (this.studentDataGroup.controls["lastschool_schoolyear"].value >="2013-2014"){
                this.studentDataGroup.controls["regionaddress"].setValidators(null);
                this.studentDataGroup.controls["regiontk"].setValidators(null);
                this.studentDataGroup.controls["regionarea"].setValidators(null);
                this.studentDataGroup.controls["am"].setValidators( [Validators.required]);

                this.studentDataGroup.controls["am"].updateValueAndValidity();
                this.studentDataGroup.controls["regionaddress"].updateValueAndValidity();
                this.studentDataGroup.controls["regiontk"].updateValueAndValidity();
                this.studentDataGroup.controls["regionarea"].updateValueAndValidity();
            }
            else{
                this.studentDataGroup.controls["regionaddress"].setValidators([Validators.pattern(VALID_ADDRESS_PATTERN), Validators.required]);
                this.studentDataGroup.controls["regiontk"].setValidators([Validators.pattern(VALID_ADDRESSTK_PATTERN), Validators.required]);
                this.studentDataGroup.controls["regionarea"].setValidators([Validators.pattern(VALID_NAMES_PATTERN), Validators.required]);
                this.studentDataGroup.controls["am"].setValidators(null);

                this.studentDataGroup.controls["am"].updateValueAndValidity();
                this.studentDataGroup.controls["regionaddress"].updateValueAndValidity();
                this.studentDataGroup.controls["regiontk"].updateValueAndValidity();
                this.studentDataGroup.controls["regionarea"].updateValueAndValidity();
            }
        }
    }

}
