import { List } from "immutable";
import { TypedRecord } from "typed-immutable-record";

export interface IDataMode {
    edit: boolean;
    edit_class: boolean;
    app_update: boolean;

    appid: string;
    currentclass: string;
    studentfirstname: string;
    studentsurname: string;
    fatherfirstname: string;
    motherfirstname: string;
    studentbirthdate: Date;
    regionaddress: string;
    regiontk: string;
    regionarea: string;
    lastschool_schoolname: any;
    lastschool_registrynumber: string;
    lastschool_unittypeid: number;
    lastschool_schoolyear: string;
    lastschool_class: string;
    relationtostudent: string;
    telnum: string;

    sector_name: string;
    course_name: string;
    epal_name_choice: string;
}

export interface IDataModeRecord extends TypedRecord<IDataModeRecord>, IDataMode { };
export type IDataModeRecords = List<IDataModeRecord>;
