import { ISchoolType, ISchoolTypeRecord, ISchoolTypeRecords } from "./schooltype.types";
import { schooltypeReducer } from "./schooltype.reducer";
import { deimmutifySchoolType } from "./schooltype.transformers";

export {
    ISchoolType,
    ISchoolTypeRecord,
    ISchoolTypeRecords,
    schooltypeReducer,
    deimmutifySchoolType,
};
