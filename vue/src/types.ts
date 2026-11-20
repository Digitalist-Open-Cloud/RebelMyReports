export interface Report {
  idreport: number;
  idsite: number;
  name: string;
  description: string;
  report_type: 'table'|'evolution'|'keyfigure';
  dimensions: string[];
  metrics: string[];
  segment_filter: string;
  category: string;
  subcategory: string;
  category_id: string;
  subcategory_id: string;
  sort_metric: string;
  sort_order: string;
  row_limit: number;
  metric_filters: { metric: string, operator: string, value: number }[];
  value_groups: ValueGroup[];
  visualization: string;
  paused: boolean;
  position: number;
  created_date: string;
  updated_date: string;
  created_by?: string;
  updated_by?: string;
}

export interface ValueGroup {
  dimension: string;
  name: string;
  operator: string;
  values: string[];
}

export interface ArchiveStatus {
  idreport: number;
  first_date: string|null;
  last_date: string|null;
  is_queued: boolean;
  queued_days: number;
  problem: { type: 'timeout'|'failed'|'truncated', period: string }|null;
}

export interface CatalogueItem {
  id: string;
  name: string;
  category: string;
  siteSpecific: boolean;
  usableInFormula?: boolean;
}

export interface CalculatedMetric {
  idmetric: number;
  id: string;
  name: string;
  formula: string;
  format: string;
}

export interface Category {
  id: string;
  name: string;
  pages: { id: string, name: string }[];
}
