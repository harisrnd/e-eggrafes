import { List } from "immutable";
import { TypedRecord } from "typed-immutable-record";

export interface IDataMode {
    //edit: boolean;
    //edit_class: boolean;
    app_update: boolean;
    appid: string;
    //studentfirstname: string;
    //studentsurname: string;
    //fatherfirstname: string;
    //motherfirstname: string;
    //studentbirthdate: Date;
    //regionaddress: string;
    //regiontk: string;
    //regionarea: string;
    //lastschool_schoolname: any;
    //lastschool_registrynumber: string;
    //lastschool_unittypeid: number;
    //lastschool_schoolyear: string;
    //lastschool_class: string;
    //relationtostudent: string;
    //telnum: string;
    currentclass: string;
    sector_id: string;
    course_id: string;
    epal_choice: string;
}

export interface IDataModeRecord extends TypedRecord<IDataModeRecord>, IDataMode { };
export type IDataModeRecords = List<IDataModeRecord>;
