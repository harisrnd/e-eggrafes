import { List } from "immutable";
import { TypedRecord } from "typed-immutable-record";

export interface IOrientationGroupObj {
		id: number;
    name: string;
    selected: boolean;
}

export interface IOrientationGroupRecord extends TypedRecord<IOrientationGroupRecord>, IOrientationGroupObj { };
export type IOrientationGroupRecords = List<IOrientationGroupRecord>;
