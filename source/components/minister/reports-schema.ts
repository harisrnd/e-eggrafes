
export class TableColumn {
    field: string;
    title: string;
    type: string;
    isDisplay: boolean;
    isExport: boolean;
    valuePrepareFunction: Function;
}

export class ReportsSchema {

    ReportUsersSchema = {
        actions: false,
        fileName: "e-ΕΠΑΛ Αριθμός Αιτήσεων - Εγγεγραμμένων Χρηστών",
        noDataMessage: "Δεν υπάρχουν δεδομένα που περιέχουν το κείμενο αναζήτησης",
        columns: {
            name: {
                title: "Περιγραφή",
                filter: false
            },
            numStudents: {
                title: "Αριθμός",
                filter: false
            }
        }
    };

    genReportSchema = {
        actions: false,
        fileName: "e-ΕΠΑΛ Κατανομή Μαθητών με Βάση τη Σειρά Προτίμησης",
        noDataMessage: "Δεν υπάρχουν δεδομένα που περιέχουν το κείμενο αναζήτησης",
        columns: {
            name: {
                title: "Κατηγορία",
                filter: false
            },
            numStudents: {
                title: "Αριθμός",
                filter: false
            }
        }
    };

    reportAllStatSchema = {
        actions: false,
        fileName: "e-ΕΠΑΛ Αναφορά",
        pager: {
            display: true,
            perPage: 10
        },
        noDataMessage: "Δεν υπάρχουν δεδομένα που περιέχουν το κείμενο αναζήτησης",
        columns: {
            name: {
                title: "Σχολείο",
                width: "18%",
                filter: false
            },
            region: {
                title: "Περιφερειακή Διεύθυνση",
                width: "15%",
                filter: false
            },
            admin: {
                title: "Διεύθυνση Εκπαίδευσης",
                width: "15%",
                filter: false
            },
            section: {
                title: "Τάξη/Τομέας/Ειδικότητα",
                width: "18%",
                filter: false
            },
            num_not_confirmed: {
                title: "Κατανεμημένοι Μαθητές",
                width: "8%",
                filter: false
            },
            num: {
                title: "Εγγεγραμμένοι Μαθητές",
                width: "8%",
                filter: false
            },
            capacity: {
                title: "Χωρ/κα",
                width: "8%",
                filter: false
            },
            percentage: {
                title: "Ποσοστό (%)",
                width: "8%",
                filter: false
            }
        }
    };

    reportNoCapacity = {
        actions: false,
        fileName: "e-ΕΠΑΛ Σχολικές μονάδες που δεν έχουν δηλώσει Χωρητικότητα τμημάτων",
        pager: {
            display: true,
            perPage: 10
        },
        noDataMessage: "Δεν υπάρχουν δεδομένα που περιέχουν το κείμενο αναζήτησης",
        columns: {
            name: {
                title: "Σχολείο",
                width: "22%",
                filter: false
            },
            region: {
                title: "Περιφερειακή Διεύθυνση",
                width: "20%",
                filter: false
            },
            admin: {
                title: "Διεύθυνση Εκπαίδευσης",
                width: "20%",
                filter: false
            },
            section: {
                title: "Τάξη/Τομέας/Ειδικότητα",
                width: "22%",
                filter: false
            },
            capacity: {
                title: "Χωρητικότητα",
                width: "15%",
                filter: false
            }
        }
    };

    reportCompletenessSchema = {
        actions: false,
        fileName: "e-ΕΠΑΛ Συνολική Πληρότητα σχολικών μονάδων ΕΠΑΛ ανά τάξη",
        noDataMessage: "Δεν υπάρχουν δεδομένα που περιέχουν το κείμενο αναζήτησης",
        columns: {
            name: {
                title: "Σχολείο",
                width: "15%",
                filter: false
            },
            region: {
                title: "ΠΔΕ",
                width: "10%",
                filter: false
            },
            admin: {
                title: "ΔΙΔΕ",
                width: "10%",
                filter: false
            },
            percTotal: {
                title: "% Σχολείου",
                width: "10%",
                filter: false
            },
            percA: {
                title: "% Α\" τάξης",
                width: "10%",
                filter: false
            },
            percB: {
                title: "% Β\" τάξης",
                width: "10%",
                filter: false
            },
            percC: {
                title: "% Γ\" τάξης",
                width: "10%",
                filter: false
            },
            percD: {
                title: "% Δ\" τάξης",
                width: "10%",
                filter: false
            }
        }
    };

    reportMergedClassesSchema = {
        actions: false,fileName: "e-ΕΠΑΛ Συγχωνέυσεις Τμημάτων",
        pager: {
            display: true,
            perPage: 5
        },
        noDataMessage: "Δεν υπάρχουν δεδομένα που περιέχουν το κείμενο αναζήτησης",
        columns: {
            nameΑ: {
                title: "Σχολείο Συγχώνευσης",
                width: "40%",
                filter: false
            },
            regionΑ: {
                title: "Περιφερειακή Διεύθυνση",
                width: "40%",
                filter: false
            },
            adminΑ: {
                title: "Διεύθυνση Εκπαίδευσης",
                width: "40%",
                filter: false
            },
            sectionΑ: {
                title: "Τάξη/Τομέας/Ειδικότητα",
                width: "40%",
                filter: false
            },
            numΑ: {
                title: "Αριθμός Μαθητών",
                width: "5%",
                filter: false
            },
            nameΒ: {
                title: "Σχολείο Υποδοχής",
                width: "40%",
                filter: false
            },
            regionΒ: {
                title: "Π.Δ.Ε. Υποδοχής",
                width: "40%",
                filter: false
            },
            adminΒ: {
                title: "ΔΙ.Δ.Ε. Υποδοχής",
                width: "40%",
                filter: false
            },
            numΒ: {
                title: "Σύνολο Μαθητών",
                width: "5%",
                filter: false
            }
        }
    };



    reportSmallClassesSchema = {
        actions: false,
        fileName: "e-ΕΠΑΛ Ολιγομελή Τμήματα",
        pager: {
            display: true,
            perPage: 10
        },
        noDataMessage: "Δεν υπάρχουν δεδομένα που περιέχουν το κείμενο αναζήτησης",
        columns: {
            name: {
                title: "Σχολείο",
                width: "18%",
                filter: false
            },
            region: {
                title: "Περιφερειακή Διεύθυνση",
                width: "15%",
                filter: false
            },
            admin: {
                title: "Διεύθυνση Εκπαίδευσης",
                width: "15%",
                filter: false
            },
            section: {
                title: "Τάξη/Τομέας/Ειδικότητα",
                width: "18%",
                filter: false
            },
            num: {
                title: "Εγγεγραμμένοι Μαθητές",
                width: "8%",
                filter: false
            },
            limit_down: {
                title: "Κατώτατο Όριο Μαθητών",
                width: "8%",
                filter: false
            },
            capacity: {
                title: "Χωρ/κα",
                width: "8%",
                filter: false
            },
            percentage: {
                title: "Ποσοστό (%)",
                width: "8%",
                filter: false
            }
        }
    };


    reportUserApplicationsSchema = {
        actions: false,
        fileName: "e-ΕΠΑΛ Αριθμός Αιτήσεων ανά Αιτούντα",
        pager: {
            display: true,
            perPage: 10
        },
        noDataMessage: "Δεν υπάρχουν δεδομένα που περιέχουν το κείμενο αναζήτησης",
        columns: {
            studentId: {
                title: "Αιτών/ούσα",
                width: "25%",
                filter: false
            },
            numapps: {
                title: "Αριθμός Αιτήσεων",
                width: "25%",
                filter: false
            }
        }
    };

    reportApplicationsSchema = {
        actions: false,
        fileName: "e-ΕΠΑΛ Αριθμός αιτησεων σχολικη μοναδα ΕΠΑΛ",
        pager: {
            display: true,
            perPage: 10
        },
        noDataMessage: "Δεν υπάρχουν δεδομένα που περιέχουν το κείμενο αναζήτησης",
        columns: {
            name: {
                title: "Σχολείο",
                width: "20%",
                filter: false
            },
            region: {
                title: "Περιφερειακή Διεύθυνση",
                width: "20%",
                filter: false
            },
            admin: {
                title: "Διεύθυνση Εκπαίδευσης",
                width: "20%",
                filter: false
            },
            section: {
                title: "Τάξη/Τομέας/Ειδικότητα",
                width: "30%",
                filter: false
            },
            num: {
                title: "Αριθμός Αιτήσεων",
                width: "8%",
                filter: false
            }
        }
    };

    reportgelStudentsSchema = {
        actions: false,
        fileName: "e-ΕΠΑΛ Αριθμός Μαθητών προερχόμενων από Γενικά Λύκεια",
        pager: {
            display: true,
            perPage: 10
        },
        noDataMessage: "Δεν υπάρχουν δεδομένα που περιέχουν το κείμενο αναζήτησης",
        columns: {
            name: {
                title: "Σχολείο",
                width: "20%",
                filter: false
            },
            region: {
                title: "Περιφερειακή Διεύθυνση",
                width: "20%",
                filter: false
            },
            admin: {
                title: "Διεύθυνση Εκπαίδευσης",
                width: "20%",
                filter: false
            },
            section: {
                title: "Τάξη/Τομέας/Ειδικότητα",
                width: "30%",
                filter: false
            },
            num: {
                title: "Αριθμός Αιτήσεων",
                width: "8%",
                filter: false
            }
        }
    };


    reportEpalCapacity = {
        actions: false,
        fileName: "e-ΕΠΑΛ Χωρητικότητα Σχολικής Μονάδας",
        pager: {
            display: true,
            perPage: 15
        },
        noDataMessage: "Δεν υπάρχουν δεδομένα που περιέχουν το κείμενο αναζήτησης",
        columns: {
            section: {
                title: "Τάξη/Τομέας/Ειδικότητα",
                width: "22%",
                filter: false
            },
            capacity: {
                title: "Χωρητικότητα",
                width: "15%",
                filter: false
            }
        }
    };

    reportDideGelDistrib = {
        actions: false,
        fileName: "Τοποθετήσεις Μαθητών από ΔΔΕ",
        pager: {
            display: true,
            perPage: 15
        },
        noDataMessage: "Δεν υπάρχουν δεδομένα που περιέχουν το κείμενο αναζήτησης",
        columns: {
            studentid: {
                title: "Id αίτησης",
                width: "8%",
                filter: false
            },
            studentam: {
                title: "ΑΜ μαθητή",
                width: "8%",
                filter: false
            },
            studentclass: {
                title: "Τάξη μαθητή",
                width: "8%",
                filter: false
            },
            studentaddress: {
                title: "Διεύθυνση κατοικίας",
                width: "33%",
                filter: false
            },
            schoolorigin: {
                title: "Σχολείο προέλευσης",
                width: "33%",
                filter: false
            },
            schooldestination: {
                title: "Σχολείο τοποθέτησης",
                width: "33%",
                filter: false
            }
        }
    };

    reportDideGelComplet = {
        actions: false,
        fileName: "Πληρότητα τμημάτων",
        pager: {
            display: true,
            perPage: 15
        },
        noDataMessage: "Δεν υπάρχουν δεδομένα που περιέχουν το κείμενο αναζήτησης",
        columns: {
            name: {
                title: "Όνομα σχολείου",
                width: "18%",
                filter: false
            },
            section: {
                title: "Τάξη Προορισμού",
                width: "18%",
                filter: false
            },
            stcount: {
                title: "Πλήθος",
                width: "10%",
                filter: false
            }
        }
    };

    reportEpalApplications = {
        actions: false,
        fileName: "e-ΕΠΑΛ Δηλώσεις μαθητών Σχολικής Μονάδας",
        pager: {
            display: true,
            perPage: 15
        },
        noDataMessage: "Δεν υπάρχουν δεδομένα που περιέχουν το κείμενο αναζήτησης",
        columns: {
            section: {
                title: "Τάξη/Τομέας/Ειδικότητα",
                width: "22%",
                filter: true
            },
            name: {
                title: "Όνομα",
                width: "15%",
                filter: true
            },
            surname: {
                title: "Επώνυμο",
                width: "15%",
                filter: true
            },
            address: {
                title: "Διεύθυνση",
                width: "15%",
                filter: true
            },
            tel: {
                title: "Τηλέφωνο",
                width: "15%",
                filter: true
            },
            confirm: {
                title: "Επιβεβαίωση",
                width: "15%",
                filter: true
            }
        },



    };



    constructor() { }

}
