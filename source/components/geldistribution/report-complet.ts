import { NgRedux } from "@angular-redux/store";
import { Injectable } from "@angular/core";
import { Component, ElementRef, Input, OnDestroy, OnInit, ViewChild } from "@angular/core";
import { ActivatedRoute, Router } from "@angular/router";
import { LocalDataSource } from "ng2-smart-table";
import { BehaviorSubject, Subscription } from "rxjs/Rx";

import { API_ENDPOINT } from "../../app.settings";
import { HelperDataService } from "../../services/helper-data-service";
//import { IAppState } from "../../store/store";
import { CsvCreator } from "../minister/csv-creator";
import { ReportsSchema, TableColumn } from "../minister/reports-schema";

@Component({
    selector: "report-gel-complet",
    template: `

    <div class="reports-container">
        <div class = "loading" *ngIf="validCreator == 0" ></div>
        <h5>Αιτήσεις Μαθητών</h5>
        <button type="submit" class="btn btn-alert pull-right"  (click)="navigateBack()" > Επιστροφή</button>

        <div *ngIf="validCreator == 1 ">
        <!--<input #search class="search" type="text" placeholder="Αναζήτηση..." (keydown.enter)="onSearch(search.value)">-->
          <div class="smart-table-container table table-hover table-striped" reportScroll>
            <ng2-smart-table [settings]="settings" [source]="source"></ng2-smart-table>
          </div>
        </div>

        <button type="button" class="alert alert-info pull-right" (click)="export2Csv()" [hidden]="validCreator != 1"><i class="fa fa-download"></i> Εξαγωγή σε csv</button>
    </div>
   `
})

@Injectable() export default class ReportGelComplet implements OnInit, OnDestroy {

    private generalReport$: BehaviorSubject<any>;
    private generalReportSub: Subscription;
    private apiEndPoint = API_ENDPOINT;
    private data;
    private validCreator: number;
    //private reportId: number;
    private routerSub: any;

    private source: LocalDataSource;
    columnMap: Map<string, TableColumn> = new Map<string, TableColumn>();
    @Input() settings: any;
    private reportSchema = new ReportsSchema();
    private csvObj = new CsvCreator();

    constructor(
        private _hds: HelperDataService,
        private activatedRoute: ActivatedRoute,
        private router: Router) {

        this.generalReport$ = new BehaviorSubject([{}]);
        this.validCreator = -1;
    }

    ngOnInit() {
        this.createReport();
    }

    ngOnDestroy() {
        if (this.generalReportSub)
            this.generalReportSub.unsubscribe();
    }

    createReport() {
        console.log("Testing..");
        this.validCreator = 0;
        let route = "/dide/didegel-complet-report";
        this.settings = this.reportSchema.reportDideGelComplet;

        this.generalReportSub = this._hds.makeDideReports(route).subscribe(data => {
            this.generalReport$.next(data);
            this.data = data;
            this.validCreator = 1;
            this.source = new LocalDataSource(this.data);
            this.columnMap = new Map<string, TableColumn>();

            // pass parametes to csv class object
            this.csvObj.columnMap = this.columnMap;
            this.csvObj.source = this.source;
            this.csvObj.settings = this.settings;
            this.csvObj.prepareColumnMap();
        },
            error => {
                this.generalReport$.next([{}]);
                this.validCreator = -1;
            });
    }


    navigateBack() {
        this.router.navigate(["/dide/didegel-reports"]);
    }

    //onSearch(query: string = "") {
    //    this.csvObj.onSearch(query);
    //}

    export2Csv() {
        this.csvObj.export2Csv();
    }
}
