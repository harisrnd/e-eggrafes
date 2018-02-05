import { List } from "immutable";
import { TypedRecord } from "typed-immutable-record";

export interface IGelClass {
    id: number;
    name: string;
    category: string;
    selected: boolean;
}

export interface IGelClassRecord extends TypedRecord<IGelClassRecord>, IGelClass { };
export type IGelClassRecords = List<IGelClassRecord>;