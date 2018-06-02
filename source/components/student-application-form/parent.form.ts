import { Component, Injectable, OnDestroy, OnInit, Renderer } from "@angular/core";
import { FormBuilder, FormGroup, Validators } from "@angular/forms";
import { Router } from "@angular/router";
import { BehaviorSubject, Subscription } from "rxjs/Rx";

import { LoginInfoActions } from "../../actions/logininfo.actions";
import { VALID_EMAIL_PATTERN, VALID_UCASE_NAMES_PATTERN } from "../../constants";
import { HelperDataService } from "../../services/helper-data-service";

@Component({
    selector: "parent-form",
    templateUrl: "parent.form.html"
})

@Injectable() export default class ParentForm implements OnInit, OnDestroy {

    public formGroup: FormGroup;
    private respond: any;
    private applicantUserData$: BehaviorSubject<any>;
    private applicantUserDataSub: Subscription;
    private showLoader: BehaviorSubject<boolean>;
    private modalTitle: BehaviorSubject<string>;
    private modalText: BehaviorSubject<string>;
    public isModalShown: BehaviorSubject<boolean>;
    public hasRight: BehaviorSubject<boolean>;
    private representativeRole: BehaviorSubject<boolean>;
    private gsisIdentSub: Subscription;

    private numAppChildren =  <number>0;
    private numAppSelf =  <number>0;
    private representVerified: BehaviorSubject<boolean>;

    constructor(private fb: FormBuilder,
        private router: Router,
        private hds: HelperDataService,
        private _prfa: LoginInfoActions,
        private rd: Renderer) {
        this.isModalShown = new BehaviorSubject(false);
        this.hasRight = new BehaviorSubject(true);
        this.representativeRole = new BehaviorSubject(false);
        this.representVerified = new BehaviorSubject(false);
        this.formGroup = this.fb.group({
            userName: ["", [Validators.pattern(VALID_UCASE_NAMES_PATTERN), Validators.required]],
            userSurname: ["", [Validators.pattern(VALID_UCASE_NAMES_PATTERN), Validators.required]],
            userFathername: ["", [Validators.pattern(VALID_UCASE_NAMES_PATTERN), Validators.required]],
            userMothername: ["", [Validators.pattern(VALID_UCASE_NAMES_PATTERN), Validators.required]],
            userEmail: ["", [Validators.pattern(VALID_EMAIL_PATTERN), Validators.required]],
            representRole: ["0",  []],
            userChildren: [, []],
        });


        //this.applicantUserData$ = new BehaviorSubject(<any>{ userEmail: "", userName: "", userSurname: "", userFathername: "", userMothername: "" , representRole: "" });
        this.applicantUserData$ = new BehaviorSubject(<any>{ userEmail: "", userName: "", userSurname: "", userFathername: "", userMothername: "" ,
                                                        representRole: "0", numAppSelf: 0, numAppChildren: 0, numChildren: 0 });
        this.showLoader = new BehaviorSubject(false);
        this.modalTitle = new BehaviorSubject("");
        this.modalText = new BehaviorSubject("");
    }

    public showModal(): void {
        (<any>$("#emailSentNotice")).modal("show");
    }

    public hideModal(): void {
        (<any>$("#emailSentNotice")).modal("hide");
    }

    public onHidden(): void {
        this.isModalShown.next(false);
    }

    public showChildrenModal(): void {
        (<any>$("#childrenSentNotice")).modal("show");
    }

    public hideChildrenModal(): void {
        (<any>$("#childrenSentNotice")).modal("hide");
    }

    public onChildrenHidden(): void {
        this.isModalShown.next(false);
    }

    ngOnInit() {
        (<any>$("#emailSentNotice")).appendTo("body");
        (<any>$("#childrenSentNotice")).appendTo("body");
        this.showLoader.next(true);

        this.gsisIdentSub = this.hds.isGSIS_ident_enabled().subscribe(z => {
            if (z.res === "1")  {
              this.formGroup.get("userName").disable();
              this.formGroup.get("userSurname").disable();
              this.formGroup.get("userFathername").disable();
              //this.formGroup.get("userMothername").disable();
            }
        });

        this.applicantUserDataSub = this.hds.getApplicantUserData().subscribe(x => {
            this.showLoader.next(false);
            this.applicantUserData$.next(x);
            this.formGroup.get("userEmail").setValue(x.userEmail);
            this.formGroup.get("userName").setValue(x.userName);
            this.formGroup.get("userSurname").setValue(x.userSurname);
            this.formGroup.get("userFathername").setValue(x.userFathername);
            this.formGroup.get("userMothername").setValue(x.userMothername);
            this.formGroup.get("representRole").setValue(Number(x.representRole));
            if (x.userEmail !== "")
              this.formGroup.get("userChildren").setValue(Number(x.numChildren));
            /*
            if (x.userEmail !== "") {
              this.formGroup.get("representRole").disable();
              this.formGroup.get("userChildren").disable();
            }
            */

            if (Number(x.representRole))
              this.representativeRole.next(true);
            else
              this.representativeRole.next(false);

            if (Number(x.verificationCodeVerified)) {
              this.representVerified.next(true);
              this.formGroup.get("representRole").disable();
              this.formGroup.get("userChildren").disable();
            }
            else
              this.representVerified.next(false);

            /*
            //if ( Number(x.numAppSelf) > 0 && Number(x.numAppChildren) >= Number(x.numChildren)   )
            if (  (Number(x.numAppChildren) + Number(x.numAppSelf) >=  Number(x.numChildren)+1 ) ||
                  (  (Number(x.numAppChildren) + Number(x.numAppSelf) >=   4)  &&   this.representativeRole.getValue() == false) )
              this.hasRight = new BehaviorSubject(false);
            */
          this.numAppSelf = Number(x.numAppSelf);
          this.numAppChildren = Number(x.numAppChildren);

        });

    }

    ngOnDestroy() {
        (<any>$("#emailSentNotice")).remove();
        (<any>$("#childrenSentNotice")).remove();
        if (this.applicantUserDataSub) this.applicantUserDataSub.unsubscribe();
        if (this.gsisIdentSub) this.gsisIdentSub.unsubscribe();
    }

    saveProfileAndContinue(): void {

        let numCh = this.formGroup.controls["userChildren"].value;
        if (  ( (this.numAppChildren + this.numAppSelf) >=  Number(numCh)+1 ) ||
           (  (this.numAppChildren + this.numAppSelf >=   5)  &&   this.representativeRole.getValue() == false) )
              this.hasRight.next(false);
        else
            this.hasRight.next(true);

        if (!this.formGroup.valid) {
            this.modalTitle.next("Αποτυχία αποθήκευσης");
            this.modalText.next("Δεν συμπληρώσατε κάποιο πεδίο");
            this.showModal();
        }
        else if ( this.formGroup.controls["userChildren"].enabled && this.formGroup.controls["userChildren"].value == null)  {
          this.modalTitle.next("Μη αποδεκτός αριθμός παιδιών");
          this.modalText.next("Συμπληρώστε τον αριθμό παιδιών για τα οποία πρόκειται να κάνετε αίτηση. ");
          this.showModal();
        }
        else if ( this.formGroup.controls["userChildren"].enabled && this.representVerified.getValue() == false &&
          parseInt(this.formGroup.controls["userChildren"].value) > 4 || parseInt(this.formGroup.controls["userChildren"].value) < 0 ) {
          this.modalTitle.next("Μη αποδεκτός αριθμός παιδιών");
          this.modalText.next("Μπορείτε να καταχωρήσετε από 0 έως και 4 παιδιά. ");
          this.showModal();
        }

        //else if (this.formGroup.controls["userChildren"].enabled) {
        /*
        else if (this.formGroup.get("userEmail").value == "")  {
            this.modalTitle.next("Επισήμανση");
            this.modalText.next("Πατώντας Επιβεβαίωση, ο αριθμός των παιδιών που δηλώνετε οριστικοποιείται. " +
                "ΔΕΝ θα έχετε το δικαίωμα να τροποποιήσετε τον αριθμό παιδιών στην επόμενη είσοδό σας στην εφαρμογή. " +
                "Το ίδιο ισχύει για την επιλογή υπευθύνου υποβολής αιτήσεων σε κέντρα κοινωνικής πρόνοιας ή κέντρα φιλοξενίας προσφύγων."
                );
            this.showChildrenModal();
        }
        */
        else if (!this.hasRight.getValue())  {
            this.modalTitle.next("Μη δικαίωμα νέας αίτησης");
            this.modalText.next("Έχετε ήδη υποβάλλει το σύνολο των αιτήσεων που δικαιούστε να κάνετε.");
            this.showModal();
        }
        /*
        else if (this.existentMail !== "" && this.representativeRole.getValue() !== this.existentRole)  {
            this.modalTitle.next("Μη δικαίωμα τροποποίησης");
            this.modalText.next("Δεν έχετε δικαίωμα να ενεργοποιήσετε / απενεργοποιήσετε την επιλογή υπευθύνου για τις αιτήσεις μαθητών " +
                                "που βρίσκονται σε ορφανοτροφεία ή κέντρα φιλοξενίας προσφύγων." +
                                " Για αλλαγή αυτής της ρύθμισης επικοινωνήστε με το διαχειριστή του συστήματος.");
            this.showModal();
        }
        */
        else {
            this.showLoader.next(true);
            //this.hds.saveProfile(this.formGroup.value)
            this.hds.saveProfile(this.formGroup.getRawValue())
                .then(res => {
                    //this._prfa.saveProfile(this.formGroup.value);
                    this._prfa.saveProfile(this.formGroup.getRawValue());
                    this.showLoader.next(false);
                    this.router.navigate(["/intro-statement"]);
                })
                .catch(err => {
                    this.showLoader.next(false);
                    console.log(err);
                });
        }
    }

    saveAnyway():void  {

      this.showLoader.next(true);
      //this.hds.saveProfile(this.formGroup.value)
      this.hds.saveProfile(this.formGroup.getRawValue())
          .then(res => {
              //this._prfa.saveProfile(this.formGroup.value);
              this._prfa.saveProfile(this.formGroup.getRawValue());
              this.showLoader.next(false);
              this.router.navigate(["/intro-statement"]);
          })
          .catch(err => {
              this.showLoader.next(false);
              console.log(err);
          });

    }

    toggleRepresentativeRole() {
        this.representativeRole.next(! (this.representativeRole.getValue() ));
    }

}
