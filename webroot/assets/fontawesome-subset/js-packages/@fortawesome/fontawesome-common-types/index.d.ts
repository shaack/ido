export type IconFamily = "classic" | "sharp" | "duotone";
export type IconPrefix = "fas" | "far" | "fal" | "fat" | "fad" | "fab" | "fak" | "fass" ;
export type CssStyleClass = "fa-solid" | "fa-regular" | "fa-light" | "fa-thin" | "fa-duotone" | "fa-brands" ;
export type IconStyle = "solid" | "regular" | "light" | "thin" | "duotone" | "brands" ;
export type IconPathData = string | string[]

export interface IconLookup {
  prefix: IconPrefix;
  // IconName is defined in the code that will be generated at build time and bundled with this file.
  iconName: IconName;
}

export interface IconDefinition extends IconLookup {
  icon: [
    number, // width
    number, // height
    string[], // ligatures
    string, // unicode
    IconPathData // svgPathData
  ];
}

export interface IconPack {
  [key: string]: IconDefinition;
}

export type IconName = 'flag' | 
  'stopwatch' | 
  'star' | 
  'circle' | 
  'minus' | 
  'subtract' | 
  'plus' | 
  'add' | 
  'plus-minus' | 
  'notdef' | 
  'flag' | 
  'stopwatch' | 
  'star' | 
  'circle' | 
  'minus' | 
  'subtract' | 
  'plus' | 
  'add' | 
  'plus-minus' | 
  'notdef';
