import { List } from "immutable";
import { TypedRecord } from "typed-immutable-record";

export interface IStudentDataField {
    am: string;
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

export interface IStudentDataFieldRecord extends TypedRecord<IStudentDataFieldRecord>, IStudentDataField { };
export type IStudentDataFieldRecords = List<IStudentDataFieldRecord>;
