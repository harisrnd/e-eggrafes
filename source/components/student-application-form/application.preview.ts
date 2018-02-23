import { NgRedux } from "@angular-redux/store";
import { Injectable } from "@angular/core";
import { Component, OnInit } from "@angular/core";
import { Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";

import { EPALCLASSES_INITIAL_STATE } from "../../store/epalclasses/epalclasses.initial-state";
import { IEpalClassRecords } from "../../store/epalclasses/epalclasses.types";
import { REGION_SCHOOLS_INITIAL_STATE } from "../../store/regionschools/regionschools.initial-state";
import { IRegionRecords, IRegionSchoolRecord } from "../../store/regionschools/regionschools.types";
import { SECTOR_COURSES_INITIAL_STATE } from "../../store/sectorcourses/sectorcourses.initial-state";
import { ISectorRecords } from "../../store/sectorcourses/sectorcourses.types";
import { SECTOR_FIELDS_INITIAL_STATE } from "../../store/sectorfields/sectorfields.initial-state";
import { ISectorFieldRecords } from "../../store/sectorfields/sectorfields.types";

import { SCHOOLTYPE_INITIAL_STATE } from "../../store/schooltype/schooltype.initial-state";
import { ISchoolType, ISchoolTypeRecord, ISchoolTypeRecords } from "../../store/schooltype/schooltype.types";
import { GELCLASSES_INITIAL_STATE } from "../../store/gelclasses/gelclasses.initial-state";
import { IGelClassRecords } from "../../store/gelclasses/gelclasses.types";
import { IElectiveCourseFieldRecord, IElectiveCourseFieldRecords } from "../../store/electivecoursesfields/electivecoursesfields.types";
import { IOrientationGroupRecords } from "../../store/orientationgroup/orientationgroup.types";
import { ORIENTATIONGROUP_INITIAL_STATE } from "../../store/orientationgroup/orientationgroup.initial-state";

import { IAppState } from "../../store/store";
import { ELECTIVECOURSE_FIELDS_INITIAL_STATE } from "../../store/electivecoursesfields/electivecoursesfields.initial-state";


@Component({
    selector: "application-preview-select",
    template: `

        <div *ngFor="let schooltypeselected$ of schooltype$ | async;">
        <h4 style="margin-top: 20px; line-height: 2em;">Οι επιλογές μου</h4>
        <ul class="list-group left-side-view" style="margin-bottom: 20px;">
            <li class="list-group-item active">
                Τυπος Σχολείου στο νέο σχολικό έτος
            </li>
            <li class="list-group-item">
                {{schooltypeselected$.get("name")}}
            </li>
        </ul>
        </div>

        <div *ngFor="let gelclass$ of gelclasses$ | async;">
        <ul *ngIf= "gelclass$.selected===true" class="list-group left-side-view" style="margin-bottom: 20px;">
            <li class="list-group-item active">
                Τάξη φοίτησης στο νέο σχολικό έτος
            </li>
            <li class="list-group-item">
                {{gelclass$.name}} - {{gelclass$.category}}
            </li>
        </ul>
        </div>

        <div *ngFor="let or_group$ of OrientationGroup$ | async;">
        <ul *ngIf= "or_group$.selected===true" class="list-group left-side-view" style="margin-bottom: 20px;">
            <li class="list-group-item active">
                Ομάδα Προσανατολισμού
            </li>
            <li class="list-group-item">
                {{or_group$.name}}
            </li>
        </ul>
        </div>

        <ul *ngIf="(selectedCourses$ | async).length>0" class="list-group left-side-view" style="margin-bottom: 20px;">
        <li class="list-group-item active">
            Μάθημα Επιλογής
        </li>
        <div *ngFor="let selectedCourse$ of selectedCourses$ | async; let i=index; let isOdd=odd; let isEven=even">
            <li class="list-group-item" [class.oddout]="isOdd" [class.evenout]="isEven">
                <span class="roundedNumber">{{(i+1)}}</span>{{selectedCourse$.name}}
            </li>
        </div>
        </ul>

        <div *ngFor="let epalclass$ of epalclasses$ | async;">
        <ul class="list-group left-side-view" style="margin-bottom: 20px;">
                <li class="list-group-item active">
                    Τάξη φοίτησης στο νέο σχολικό έτος
                </li>
                <li class="list-group-item" *ngIf="epalclass$.get('name') === '1'">
                    Α’ Λυκείου
                </li>
                <li class="list-group-item" *ngIf="epalclass$.get('name') === '2'">
                    Β’ Λυκείου
                </li>
                <li class="list-group-item" *ngIf="epalclass$.get('name') === '3'">
                    Γ’ Λυκείου
                </li>
                <li class="list-group-item" *ngIf="epalclass$.get('name') === '4'">
                    Δ’ Λυκείου
                </li>
        </ul>
        </div>

        <div *ngFor="let sectorField$ of sectorFields$ | async">
        <ul class="list-group left-side-view">
            <li class="list-group-item active" *ngIf="sectorField$.get('selected') === true" >
                {{sectorField$.get("name")}}
            </li>
            </ul>
        </div>

    <div *ngFor="let sector$ of sectors$  | async;">
            <ul class="list-group left-side-view" style="margin-bottom: 20px;" *ngIf="sector$.get('sector_selected') === true">
                <li class="list-group-item active" *ngIf="sector$.get('sector_selected') === true" >
                    {{sector$.get("sector_name") }}
                </li>
        <div *ngFor="let course$ of sector$.courses;" >

                <li class="list-group-item" *ngIf="course$.selected === true">
                    {{course$.get("course_name")   }}
                </li>

        </div>
            </ul>
        </div>

        <ul *ngIf="(selectedSchools$ | async)" class="list-group left-side-view" style="margin-bottom: 20px;">

                <div *ngFor="let epal$ of selectedSchools$ | async; let i=index; let isOdd=odd; let isEven=even" >

                <li class="list-group-item" [class.oddout]="isOdd" [class.evenout]="isEven">
                    <span class="roundedNumber">{{(i+1)}}</span>&nbsp;&nbsp;{{epal$.get("epal_name")}}
                </li>
              </div>
        </ul>
  `
})

@Injectable() export default class ApplicationPreview implements OnInit {
    private sectors$: BehaviorSubject<ISectorRecords>;
    private regions$: BehaviorSubject<IRegionRecords>;
    private selectedSchools$: BehaviorSubject<Array<IRegionSchoolRecord>> = new BehaviorSubject(Array());
    private sectorFields$: BehaviorSubject<ISectorFieldRecords>;
    private epalclasses$: BehaviorSubject<IEpalClassRecords>;
    private epalclassesSub: Subscription;
    private sectorsSub: Subscription;
    private regionsSub: Subscription;
    private sectorFieldsSub: Subscription;

    private schooltype$: BehaviorSubject<ISchoolTypeRecords>;
    private gelclasses$: BehaviorSubject<IGelClassRecords>;
    private electivecourses$: BehaviorSubject<IElectiveCourseFieldRecords>;
    private OrientationGroup$: BehaviorSubject<IOrientationGroupRecords>;
    private OrientationGroupSub: Subscription;
    private electivecoursesSub: Subscription;
    private schooltypeSub: Subscription;
    private gelclassesSub: Subscription;

    private courseActive = "-1";
    private numSelectedSchools = <number>0;
    private numSelectedOrder = <number>0;
    private classSelected = 0;
    private currentUrl: string;

    private electivecourseSelected = <number>0;
    private selectedCourses$: BehaviorSubject<Array<IElectiveCourseFieldRecord>> = new BehaviorSubject(Array());


    constructor(private _ngRedux: NgRedux<IAppState>,
        private router: Router
    ) {

        this.regions$ = new BehaviorSubject(REGION_SCHOOLS_INITIAL_STATE);
        this.epalclasses$ = new BehaviorSubject(EPALCLASSES_INITIAL_STATE);

        this.sectors$ = new BehaviorSubject(SECTOR_COURSES_INITIAL_STATE);
        this.sectorFields$ = new BehaviorSubject(SECTOR_FIELDS_INITIAL_STATE);

        this.schooltype$ = new BehaviorSubject(SCHOOLTYPE_INITIAL_STATE);
        this.gelclasses$ = new BehaviorSubject(GELCLASSES_INITIAL_STATE);
        this.OrientationGroup$ = new BehaviorSubject(ORIENTATIONGROUP_INITIAL_STATE);
        this.electivecourses$= new BehaviorSubject(ELECTIVECOURSE_FIELDS_INITIAL_STATE);

    };

    ngOnInit() {
        window.scrollTo(0, 0);

        this.currentUrl = this.router.url;
        this.sectorsSub = this._ngRedux.select("sectors")
            .map(sectors => <ISectorRecords>sectors)
            .subscribe(scs => {
                scs.reduce((prevSector, sector) => {
                    sector.get("courses").reduce((prevCourse, course) => {
                        if (course.get("selected") === true) {
                            this.courseActive = course.get("course_id");
                        }

                        return course;
                    }, {});
                    return sector;
                }, {});
                this.sectors$.next(scs);
            });

        this.regionsSub = this._ngRedux.select("regions")
            .subscribe(regions => {
                let rgns = <IRegionRecords>regions;
                let numsel = 0, numsel2 = 0;
                let selectedSchools = Array<IRegionSchoolRecord>();
                rgns.reduce((prevRegion, region) => {
                    region.get("epals").reduce((prevEpal, epal) => {
                        if (epal.get("selected") === true) {
                            numsel++;
                            selectedSchools.push(epal);
                        }
                        if (epal.get("order_id") !== 0) {
                            numsel2++;
                        }
                        return epal;
                    }, {});
                    return region;
                }, {});
                this.numSelectedSchools = numsel;
                this.numSelectedOrder = numsel2;
                this.selectedSchools$.next(selectedSchools.sort(this.compareSchools));
            });

        this.sectorFieldsSub = this._ngRedux.select("sectorFields")
            .subscribe(sectorFields => {
                this.sectorFields$.next(<ISectorFieldRecords>sectorFields);
            }, error => { console.log("error selecting sectorFields"); });

        this.epalclassesSub = this._ngRedux.select("epalclasses")
            .subscribe(epalclasses => {
                let ecs = <IEpalClassRecords>epalclasses;
                ecs.reduce(({}, epalclass) => {
                    if (epalclass.get("name") === "Α' Λυκείου")
                        this.classSelected = 1;
                    else if (epalclass.get("name") === "Β' Λυκείου")
                        this.classSelected = 2;
                    else if (epalclass.get("name") === "Γ' Λυκείου")
                        this.classSelected = 3;
                    else if (epalclass.get("name") === "Δ' Λυκείου")
                        this.classSelected = 4;
                    return epalclass;
                }, {});
                this.epalclasses$.next(ecs);
            }, error => { console.log("error selecting epalclasses"); });

            this.schooltypeSub = this._ngRedux.select("schooltype")
            .map(schooltype => <ISchoolTypeRecords>schooltype)
            .subscribe(ecs => {
                if (ecs.size > 0) {
                      ecs.reduce(({}, type) => {
                        return type;
                    }, {});
                } else {
                    //this.formGroup.controls["typeId"].setValue("0");
                }
                this.schooltype$.next(ecs);
            }, error => { console.log("error selecting schooltype"); });

         this.gelclassesSub = this._ngRedux.select("gelclasses")
            .subscribe(gelclasses => {
                let ecs = <IGelClassRecords>gelclasses;
                ecs.reduce(({}, gelclass) => {
                    if (gelclass.get("name") === "Α' Λυκείου - ΗΜΕΡΗΣΙΟ")
                        this.classSelected = 1;
                    else if (gelclass.get("name") === "Β' Λυκείου")
                        this.classSelected = 2;
                    else if (gelclass.get("name") === "Γ' Λυκείου")
                        this.classSelected = 3;
                    else if (gelclass.get("name") === "A' Λυκείου")
                        this.classSelected = 4;
                    else if (gelclass.get("name") === "B' Λυκείου")
                        this.classSelected = 5;
                    else if (gelclass.get("name") === "Γ' Λυκείου")
                        this.classSelected = 6;
                    else if (gelclass.get("name") === "Δ' Λυκείου")
                        this.classSelected = 7;
                    return gelclass;
                }, {});
                this.gelclasses$.next(ecs);
            }, error => { console.log("error selecting gelclasses"); });


            this.electivecoursesSub = this._ngRedux.select("electivecourseFields")
            .map(electivecourseFields => <IElectiveCourseFieldRecords>electivecourseFields)
            .subscribe(sfds => {
                this.electivecourseSelected = 0;
                let selectedCourses = Array<IElectiveCourseFieldRecord>();
                sfds.reduce(({}, electivecourseField) => {
                    if (electivecourseField.get("selected") === true) {
                        ++this.electivecourseSelected;
                        selectedCourses.push(electivecourseField.toJS());
                    }

                    return electivecourseField;
                }, {});
                this.electivecourses$.next(sfds);
                selectedCourses.sort(this.compareCourses);
                for (let i = 0; i < selectedCourses.length; i++)
                    selectedCourses[i].order_id = i + 1;

                this.selectedCourses$.next(selectedCourses);
            }, error => { console.log("error selecting electivecourseFields"); });


        this.OrientationGroupSub = this._ngRedux.select("orientationGroup")
            .map(orientationGroup => <IOrientationGroupRecords>orientationGroup)
            .subscribe(ogs => {
                  this.OrientationGroup$.next(ogs);
            }, error => { console.log("error selecting orientation"); });

    }

    compareSchools(a: IRegionSchoolRecord, b: IRegionSchoolRecord) {
        if (a.order_id < b.order_id)
            return -1;
        if (a.order_id > b.order_id)
            return 1;
        return 0;
    }

    compareCourses(a: IElectiveCourseFieldRecord, b: IElectiveCourseFieldRecord) {
        if (a.order_id < b.order_id)
            return -1;
        if (a.order_id > b.order_id)
            return 1;
        return 0;
    }

    ngOnDestroy() {
        if (this.regionsSub) {
            this.regionsSub.unsubscribe();
        }
        if (this.sectorsSub) {
            this.sectorsSub.unsubscribe();
        }
        if (this.sectorFieldsSub) {
            this.sectorFieldsSub.unsubscribe();
        }
        if (this.epalclassesSub) {
            this.epalclassesSub.unsubscribe();
        }

        if (this.electivecoursesSub){
            this.electivecoursesSub.unsubscribe();
        }
        if (this.OrientationGroupSub){
            this.OrientationGroupSub.unsubscribe();
        }
        if (this.gelclassesSub){
            this.gelclassesSub.unsubscribe();
        }
        if (this.schooltypeSub){
            this.schooltypeSub.unsubscribe();
        }

    }

}
