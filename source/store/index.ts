import { createLogger } from "redux-logger";

import { IAppState, rootReducer, deimmutify } from "./store";
import { ISectorFieldRecord, ISectorFieldRecords } from "./sectorfields/sectorfields.types";
import { IRRegion, IRRegionSchool, IRegionRecord, IRegionRecords, IRegionSchoolRecord, IRegionSchoolRecords } from "./regionschools/regionschools.types";
import { ISectorRecords, ISectorRecord, ISector, ISectorCourseRecords, ISectorCourseRecord, ISectorCourse } from "./sectorcourses/sectorcourses.types";
import { IStudentDataFieldRecord, IStudentDataFieldRecords } from "./studentdatafields/studentdatafields.types";
import { IEpalClass, IEpalClassRecord, IEpalClassRecords } from "./epalclasses/epalclasses.types";
import { ILoginInfoObj, ILoginInfoRecord, ILoginInfoRecords } from "./logininfo/logininfo.types";
import { IDataMode, IDataModeRecord, IDataModeRecords } from "./datamode/datamode.types";

import { IOrientationGroupObj, IOrientationGroupRecord, IOrientationGroupRecords } from "../store/orientationgroup/orientationgroup.types";
import { IElectiveCourseFieldRecord, IElectiveCourseFieldRecords } from "./electivecoursesfields/electivecoursesfields.types";
import { ILangCourseFieldRecord, ILangCourseFieldRecords } from "./langcoursesfields/langcoursesfields.types";
import { IGelClass, IGelClassRecord, IGelClassRecords } from "./gelclasses/gelclasses.types";
import { IGelStudentDataFieldRecord, IGelStudentDataFieldRecords } from "./gelstudentdatafields/gelstudentdatafields.types";

export {
    IAppState,
    rootReducer,
    ISectorFieldRecord,
    ISectorFieldRecords,
    IRRegion,
    IRegionRecord,
    IRegionRecords,
    IRegionSchoolRecord,
    IRegionSchoolRecords,
    IRRegionSchool,
    ISectorRecords,
    ISectorRecord,
    ISector,
    ISectorCourseRecords,
    ISectorCourseRecord,
    ISectorCourse,
    IStudentDataFieldRecord,
    IStudentDataFieldRecords,
    IEpalClass,
    IEpalClassRecord,
    IEpalClassRecords,
    IDataMode,
    IDataModeRecord,
    IDataModeRecords,
    ILoginInfoObj,
    ILoginInfoRecord,
    ILoginInfoRecords,

    IOrientationGroupObj,
    IOrientationGroupRecord,
    IOrientationGroupRecords,
    IElectiveCourseFieldRecord,
    IElectiveCourseFieldRecords,
    ILangCourseFieldRecord,
    ILangCourseFieldRecords,
    IGelClass,
    IGelClassRecord,
    IGelClassRecords,
    IGelStudentDataFieldRecord,
    IGelStudentDataFieldRecords
};

const myLogger = createLogger({
    level: "info",
    collapsed: true,
    stateTransformer: deimmutify
});

export const middleware = [
    myLogger
];
