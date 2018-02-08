import { List } from "immutable";
import { recordify } from "typed-immutable-record";

import { ORIENTATIONGROUP_INIT,  ORIENTATIONGROUP_RECEIVED, ORIENTATIONGROUP_SAVE } from "../../constants";
import { ORIENTATIONGROUP_INITIAL_STATE } from "./orientationgroup.initial-state";
import { IOrientationGroupObj, IOrientationGroupRecord, IOrientationGroupRecords } from "./orientationgroup.types";

export function OrientationGroupReducer(state: IOrientationGroupRecords = ORIENTATIONGROUP_INITIAL_STATE, action): IOrientationGroupRecords {
    switch (action.type) {
        case ORIENTATIONGROUP_RECEIVED:
           let orientalGroupFieldss = Array<IOrientationGroupRecord>();
            let i = 0;
            action.payload.orientat.forEach(orientalGroupField =>
            {
                orientalGroupFieldss.push(recordify<IOrientationGroupObj, IOrientationGroupRecord>(
                    { id: orientalGroupField.id, name: orientalGroupField.name, selected: false }
                    ));
                i++;
            });
            return List(orientalGroupFieldss);

        /*
        case ORIENTATIONGROUP_SAVE:
                return state.withMutations(function(list) {
                if (action.payload.prevChoice >= 0)
                    list.setIn([action.payload.prevChoice, "selected"], false);
                if (action.payload.newChoice >= 0)
                    list.setIn([action.payload.newChoice, "selected"], true);
            });
        */



        case ORIENTATIONGROUP_SAVE:
                return state.withMutations(function(list) {
                      if (action.payload.isSelected === 1)
                          list.setIn([action.payload.newChoice, "selected"], false);
                      else
                          list.setIn([action.payload.newChoice, "selected"], true);
                  });


        case ORIENTATIONGROUP_INIT:
            return ORIENTATIONGROUP_INITIAL_STATE;
        default:return state;
    }


};
