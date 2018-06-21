import { List } from "immutable";
import { recordify } from "typed-immutable-record";

import { DATAMODE_INIT, DATAMODE_SAVE } from "../../constants";
import { DATAMODE_INITIAL_STATE } from "./datamode.initial-state";
import { IDataMode, IDataModeRecord, IDataModeRecords } from "./datamode.types";

export function datamodeReducer(state: IDataModeRecords = DATAMODE_INITIAL_STATE, action): IDataModeRecords {

    switch (action.type) {
        case DATAMODE_SAVE:
            let newDataMode = Array<IDataModeRecord>();
            newDataMode.push(recordify<IDataMode, IDataModeRecord>({
                /*edit: action.payload.dataMode.edit, edit_class: action.payload.dataMode.edit_class,*/
                app_update: action.payload.dataMode.app_update, appid: action.payload.dataMode.appid, apptype: action.payload.dataMode.apptype,
                /*studentfirstname: action.payload.dataMode.studentfirstname,
                studentsurname: action.payload.dataMode.studentsurname, fatherfirstname: action.payload.dataMode.fatherfirstname,
                motherfirstname: action.payload.dataMode.motherfirstname, studentbirthdate: action.payload.dataMode.studentbirthdate,
                regionaddress: action.payload.dataMode.regionaddress, regiontk: action.payload.dataMode.regiontk,
                regionarea: action.payload.dataMode.regionarea, lastschool_schoolname: action.payload.dataMode.lastschool_schoolname,
                lastschool_registrynumber: action.payload.dataMode.lastschool_registrynumber, lastschool_unittypeid: action.payload.dataMode.lastschool_unittypeid,
                lastschool_schoolyear: action.payload.dataMode.lastschool_schoolyear, lastschool_class: action.payload.dataMode.lastschool_class,
                relationtostudent: action.payload.dataMode.relationtostudent, telnum: action.payload.dataMode.telnum,*/
                sector_id: action.payload.dataMode.sector_id, course_id: action.payload.dataMode.course_id,
                epal_choice: action.payload.dataMode.epal_choice, currentclass: action.payload.dataMode.currentclass
              }));
            return List(newDataMode);

        case DATAMODE_INIT:
            return DATAMODE_INITIAL_STATE;
        default: return state;
    }
};
