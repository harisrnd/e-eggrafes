import { combineReducers } from "redux";

import * as epalclasses from "./epalclasses";
import * as loginInfo from "./logininfo";
import * as regions from "./regionschools";
import * as sectors from "./sectorcourses";
import * as sectorFields from "./sectorfields";
import * as studentDataFields from "./studentdatafields";
import * as datamode from "./datamode";

import * as orientationGroup from "./orientationgroup";
import * as electivecourseFields from "./electivecoursesfields";
import * as langcourseFields from "./langcoursesfields";
import * as gelclasses from "./gelclasses";
import * as gelstudentDataFields from "./gelstudentdatafields";
import * as schooltype from "./schooltype";


export interface IAppState {
    sectorFields?: sectorFields.ISectorFieldRecords;
    regions?: regions.IRegionRecords;
    sectors?: sectors.ISectorRecords;
    studentDataFields?: studentDataFields.IStudentDataFieldRecords;
    epalclasses?: epalclasses.IEpalClassRecords;
    loginInfo?: loginInfo.ILoginInfoRecords;
    datamode?: datamode.IDataModeRecords;

    orientationGroup?: orientationGroup.IOrientationGroupRecords;
    electivecourseFields?: electivecourseFields.IElectiveCourseFieldRecords;
    langcourseFields?: langcourseFields.ILangCourseFieldRecords;
    gelclasses?: gelclasses.IGelClassRecords;
    gelstudentDataFields?: gelstudentDataFields.IGelStudentDataFieldRecords;
    schooltype?: schooltype.ISchoolTypeRecords
};

export const rootReducer = combineReducers<IAppState>({
    sectorFields: sectorFields.sectorFieldsReducer,
    regions: regions.regionSchoolsReducer,
    sectors: sectors.sectorCoursesReducer,
    studentDataFields: studentDataFields.studentDataFieldsReducer,
    epalclasses: epalclasses.epalclassesReducer,
    loginInfo: loginInfo.loginInfoReducer,
    datamode: datamode.datamodeReducer,

    orientationGroup: orientationGroup.OrientationGroupReducer,
    electivecourseFields: electivecourseFields.electivecourseFieldsReducer,
    langcourseFields: langcourseFields.langcourseFieldsReducer,
    gelclasses: gelclasses.gelclassesReducer,
    gelstudentDataFields: gelstudentDataFields.gelstudentDataFieldsReducer,
    schooltype: schooltype.schooltypeReducer,
});

export function deimmutify(state: IAppState): Object {
    return {
        sectorFields: sectorFields.deimmutifySectorFields(state.sectorFields),
        regions: regions.deimmutifyRegionSchools(state.regions),
        sectors: sectors.deimmutifySectorCourses(state.sectors),
        studentdataFields: studentDataFields.deimmutifyStudentDataFields(state.studentDataFields),
        epalclasses: epalclasses.deimmutifyEpalClasses(state.epalclasses),
        loginInfo: loginInfo.deimmutifyLoginInfo(state.loginInfo),
        datamode: datamode.deimmutifyDataMode(state.datamode),

        orientationGroup: orientationGroup.deimmutifyOrientationGroup (state.orientationGroup),
        electivecourseFields: electivecourseFields.deimmutifyElectiveCourseFields(state.electivecourseFields),
        langcourseFields: langcourseFields.deimmutifyLangCourseFields(state.langcourseFields),
        gelclasses: gelclasses.deimmutifyGelClasses(state.gelclasses),
        gelstudentdataFields: gelstudentDataFields.deimmutifyGelStudentDataFields(state.gelstudentDataFields),
        schooltype: schooltype.deimmutifySchoolType(state.schooltype),

    };
}
