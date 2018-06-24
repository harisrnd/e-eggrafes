import { Component, OnDestroy, OnInit, Input } from "@angular/core";
import { Injectable } from "@angular/core";
import { ActivatedRoute, Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";

import { HelperDataService } from "../../services/helper-data-service";

import { CsvCreator } from "./../minister/csv-creator";
import { ReportsSchema, TableColumn } from ".//../minister/reports-schema";
import { LocalDataSource } from "ng2-smart-table";


@Component({
    selector: "directorgym-view",
    template: `

    <div class="reports-container">
      <div class = "loading" *ngIf="validCreator == 0" ></div>

      <div style="min-height: 500px;">
      <p style="margin-top: 20px; line-height: 2em;"> H παρακάτω λίστα περιλαμβάνει τους μαθητές της Γ 'ταξης Γυμνασίου του σχολείου σας που εχουν υποβάλει αίτηση δήλωση προτίμησης για εγγραφή σε ΓΕΛ/ΕΠΑΛ και για τους οποίους έχει εκδοθεί απολυτήριο. </p>
      <p style="margin-top: 20px; line-height: 2em;"> Παρακαλούμε ελέγξτε τη λίστα και επικοινωνήστε με την οικεία Διεύθυνση Δευτεροβάθμιας Εκπαίδευσης σε περίπτωση που εντοπίσετε ελλείψεις. </p>


      <div *ngIf="validCreator == 1">
        <div class="smart-table-container table table-hover table-striped">
          <ng2-smart-table [settings]="settings" [source]="source"></ng2-smart-table>
        </div>
      </div>
      <button type="button" class="alert alert-info pull-right" (click)="export2Csv()" [hidden]="validCreator != 1"><i class="fa fa-download"></i> Εξαγωγή σε csv</button>

    </div>


    `
})

@Injectable() export default class DirectorViewGym implements OnInit, OnDestroy {


    private showLoader: BehaviorSubject<boolean>;

    private generalReportSub: Subscription;
    private generalReport$: BehaviorSubject<any>;
    private data: any;
    private source: LocalDataSource;
    private validCreator: number;


    columnMap: Map<string, TableColumn> = new Map<string, TableColumn>();
    @Input() settings: any;
    private reportSchema = new ReportsSchema();

    // csvObj:CsvCreator ;
    private csvObj = new CsvCreator();

    constructor(
        private _hds: HelperDataService,
        private activatedRoute: ActivatedRoute,
        private router: Router
    ) {
        this.showLoader = new BehaviorSubject(false);
        this.generalReport$ = new BehaviorSubject([{}]);

    }


    ngOnDestroy() {
        if (this.generalReportSub)
        this.generalReportSub.unsubscribe();
    }

    ngOnInit() {
      
      this.validCreator = 0;
      this.showLoader.next(true);


        this.settings = this.reportSchema.reportGymDirector;
        this.settings.fileName = "e-eggrafes Κατανομή μαθητών Σχολικής Μονάδας";

        this.generalReportSub = this._hds.makeGymReport().subscribe(data => {
          this.generalReport$.next(data);
          this.data = data;
          this.validCreator = 1;
          this.showLoader.next(false);


          this.source = new LocalDataSource(this.data);
          this.columnMap = new Map<string, TableColumn>();

          // pass parametes to csv class object
          this.csvObj.columnMap = this.columnMap;
          this.csvObj.source = this.source;
          this.csvObj.settings = this.settings;
          // this.prepareColumnMap();
          this.csvObj.prepareColumnMap();
      },
      error => {
          this.generalReport$.next([{}]);
          this.showLoader.next(false);
          console.log("Error Getting generalReport");
      });

    }




    export2Csv() {
      this.csvObj.export2Csv();
  }

}
