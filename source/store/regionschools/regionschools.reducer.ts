import { List } from "immutable";
import { recordify } from "typed-immutable-record";

import {
    REGIONSCHOOLS_INIT,
    REGIONSCHOOLS_ORDER_SAVE,
    REGIONSCHOOLS_RECEIVED,
    REGIONSCHOOLS_SELECTED_SAVE,
    REGIONSCHOOLS_SELECTED_SAVE_WITHIDS,
} from "../../constants";
import { REGION_SCHOOLS_INITIAL_STATE } from "./regionschools.initial-state";
import {
    IRegionRecord,
    IRegionRecords,
    IRegionSchoolRecord,
    IRegionSchoolRecords,
    IRRegion,
    IRRegionSchool,
} from "./regionschools.types";

export function regionSchoolsReducer(state: IRegionRecords = REGION_SCHOOLS_INITIAL_STATE, action): IRegionRecords {
    switch (action.type) {
        case REGIONSCHOOLS_RECEIVED:
            let newRegions = Array<IRegionRecord>();
            let newEpals = Array<IRegionSchoolRecord>();
            let i = 0, j = 0;
            let ii = 0;

            action.payload.regions.forEach(region => {
                region.epals.forEach(epal => {
                    newEpals.push(recordify<IRRegionSchool, IRegionSchoolRecord>({ epal_id: epal.epal_id, epal_name: epal.epal_name, epal_special_case: epal.epal_special_case, globalIndex: epal.globalIndex, selected: epal.selected, order_id: epal.order_id }));
                    ii++;
                });
                newRegions.push(recordify<IRRegion, IRegionRecord>({ region_id: region.region_id, region_name: region.region_name, epals: List(newEpals) }));
                newEpals = Array<IRegionSchoolRecord>();
                i++;
            });
            return List(newRegions);
        case REGIONSCHOOLS_SELECTED_SAVE:
            return state.withMutations(function(list) {
                list.setIn([action.payload.rIndex, "epals"], list.get(action.payload.rIndex).get("epals").setIn([action.payload.sIndex, "selected"], action.payload.checked));
                list.setIn([action.payload.rIndex, "epals"], list.get(action.payload.rIndex).get("epals").setIn([action.payload.sIndex, "order_id"], action.payload.orderId));
            });
        case REGIONSCHOOLS_SELECTED_SAVE_WITHIDS:
            return state.withMutations(function(list) {

                list.reduce((test,lista)=>{
                    const indexOfListingToUpdate = lista.get('epals').findIndex(listing => {
                        return listing.get('epal_id') === action.payload.school_id;});
                        if (indexOfListingToUpdate>0){
                            console.log(action.payload.school_id);
                            console.log(indexOfListingToUpdate);
                        }
                    return lista;   

                },{});
        
                //console.log(indexOfListingToUpdate);

                //list.setIn([action.payload.rIndex, "epals"], list.get(action.payload.rIndex).get("epals").setIn([action.payload.sIndex, "selected"], action.payload.checked));
                //list.setIn([action.payload.rIndex, "epals"], list.get(action.payload.rIndex).get("epals").setIn([action.payload.sIndex, "order_id"], action.payload.orderId));
            });
        case REGIONSCHOOLS_ORDER_SAVE:
            let newState2 = Array<IRegionRecord>();
            newEpals = Array<IRegionSchoolRecord>();

            i = 0, j = 0;
            let ind2 = 0;
            state.forEach(region => {
                let epals: IRegionSchoolRecords;

                epals = region.get("epals");
                epals.forEach(epal => {
                    let newOrderId = epal.order_id;
                    for (let jjj = 0; jjj < 3; jjj++) {
                        if (typeof action.payload.selectedSchools[jjj] !== "undefined" &&
                            epal.globalIndex === action.payload.selectedSchools[jjj].globalIndex) {
                            newOrderId = action.payload.selectedSchools[jjj].order_id;
                            break;
                        }
                    }
                    newEpals.push(recordify<IRRegionSchool, IRegionSchoolRecord>({ epal_id: epal.epal_id, epal_name: epal.epal_name, epal_special_case: epal.epal_special_case, globalIndex: epal.globalIndex, selected: epal.selected, order_id: newOrderId }));
                });
                newState2.push(recordify<IRRegion, IRegionRecord>({ region_id: region.region_id, region_name: region.region_name, epals: List(newEpals) }));
                newEpals = Array<IRegionSchoolRecord>();
                i++;

            });

            return List(newState2);
        case REGIONSCHOOLS_INIT:
            return REGION_SCHOOLS_INITIAL_STATE;
        default: return state;
    }
};
