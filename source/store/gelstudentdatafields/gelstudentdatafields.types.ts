import { List } from "immutable";
import { TypedRecord } from "typed-immutable-record";

export interface IGelStudentDataField {
    name: string;
    studentsurname: string;
    fatherfirstname: string;
    fathersurname: string;
    motherfirstname: string;
    mothersurname: string;
    studentbirthdate: Date;
    regionaddress: string;
    regiontk: string;
    regionarea: string;
    lastschool_schoolname: any;
    lastschool_schoolyear: string;
    lastschool_class: string;
    relationtostudent: string;
    currentclass: string;
    telnum: string;
}

export interface IGelStudentDataFieldRecord extends TypedRecord<IGelStudentDataFieldRecord>, IGelStudentDataField { };
export type IGelStudentDataFieldRecords = List<IGelStudentDataFieldRecord>;
