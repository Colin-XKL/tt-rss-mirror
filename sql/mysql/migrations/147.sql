create fulltext index if not exists ttrss_entries_title_search_idx on ttrss_entries(title);
create fulltext index if not exists ttrss_entries_combined_search_idx on ttrss_entries(title, content);
