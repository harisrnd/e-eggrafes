import { List } from "immutable";
import { recordify } from "typed-immutable-record";
import { SCHOOLTYPE_INIT, SCHOOLTYPE_SAVE, SCHOOLTYPE_RECEIVED, SCHOOLTYPE_RESET } from "../../constants";
import { SCHOOLTYPE_INITIAL_STATE } from "./schooltype.initial-state";
import { ISchoolType, ISchoolTypeRecord, ISchoolTypeRecords } from "./schooltype.types";

export function schooltypeReducer(state: ISchoolTypeRecords = SCHOOLTYPE_INITIAL_STATE, action): ISchoolTypeRecords {

    switch (action.type) {

        case SCHOOLTYPE_SAVE:
        let newSchoolType = Array<ISchoolTypeRecord>();
        newSchoolType.push(recordify<ISchoolType, ISchoolTypeRecord>({ id: action.payload.schooltype_id, name: action.payload.schooltype_name}));
        return List(newSchoolType);


        case SCHOOLTYPE_INIT:
            return SCHOOLTYPE_INITIAL_STATE;
        default: return state;

    }
};
