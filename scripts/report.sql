select count(*) as total_links from links;
select count(*) as error_links from links;
select count(*) as status_count,status from link group by status order by status_count;
