import { List } from "immutable";
import { recordify } from "typed-immutable-record";

import { SECTORCOURSES_INIT, SECTORCOURSES_RECEIVED, SECTORCOURSES_SELECTED_SAVE, SECTORCOURSES_SELECTED_SAVE_WITHIDS } from "../../constants";
import { SECTOR_COURSES_INITIAL_STATE } from "./sectorcourses.initial-state";
import { ISector, ISectorCourse, ISectorCourseRecord, ISectorRecord, ISectorRecords } from "./sectorcourses.types";

export function sectorCoursesReducer(state: ISectorRecords = SECTOR_COURSES_INITIAL_STATE, action): ISectorRecords {
    switch (action.type) {
        case SECTORCOURSES_RECEIVED:
        let newSectors = Array<ISectorRecord>();
        let newCourses = Array<ISectorCourseRecord>();
        let i = 0, j = 0;
        let ii = 0;

        action.payload.sectors.forEach(sector => {
            sector.courses.forEach(course => {
                newCourses.push(recordify<ISectorCourse, ISectorCourseRecord>({ course_id: course.course_id, course_name: course.course_name, globalIndex: course.globalIndex, selected: course.selected }));
                ii++;
            });
            newSectors.push(recordify<ISector, ISectorRecord>({ sector_id: sector.sector_id, sector_name: sector.sector_name, sector_selected: sector.sector_selected, courses: List(newCourses) }));
            newCourses = Array<ISectorCourseRecord>();
            i++;
        });
        return List(newSectors);

        case SECTORCOURSES_SELECTED_SAVE:
            return state.withMutations(function(list) {
                list.setIn([action.payload.oldSIndex, "sector_selected"], false);
                list.setIn([action.payload.sIndex, "sector_selected"], true);
                list.setIn([action.payload.oldSIndex, "courses"], list.get(action.payload.oldSIndex).get("courses").setIn([action.payload.oldCIndex, "selected"], false));
                list.setIn([action.payload.sIndex, "courses"], list.get(action.payload.sIndex).get("courses").setIn([action.payload.cIndex, "selected"], action.payload.checked));
            });

        case SECTORCOURSES_SELECTED_SAVE_WITHIDS:
            return state.withMutations(function(list) {
                let sIndex=0;
                let cIndex=-1;
    
                list.reduce((test,lista)=>{
                    const cIndex = lista.get('courses').findIndex(listing => {
                        return listing.get('course_id') === action.payload.cIndex;});
                    if (cIndex>=0){
                        list.setIn([sIndex, "sector_selected"], true);
                        list.setIn([sIndex, "courses"], list.get(sIndex).get("courses").setIn([cIndex, "selected"], action.payload.checked));
                    }
    
                    sIndex++;
                    return lista;   
                }
               ,{});
               
               //list.setIn([action.payload.oldSIndex, "sector_selected"], false);
               //list.setIn([action.payload.oldSIndex, "courses"], list.get(action.payload.oldSIndex).get("courses").setIn([action.payload.oldCIndex, "selected"], false));
            });

        case SECTORCOURSES_INIT:
            return SECTOR_COURSES_INITIAL_STATE;
        default: return state;
    }
};
