-- asientos x servicio
select 
ta.id, ta.numero, b.asiento_id
from transporte_asiento ta
join transporte t on ta.transporte_id = t.id
join servicio s on t.id = s.transporte_id
left join boleto b on (b.asiento_id = ta.id)
where s.id = 1;
;

-- asientos libres x servicio
select ta.id
from transporte_asiento ta
join transporte t on ta.transporte_id = t.id
join servicio s on t.id = s.transporte_id
left join boleto b on (b.asiento_id = ta.id)
left join reserva r on b.reserva_id = r.id
where s.id = 1
and b.asiento_id is NULL
;

select b.*
from boleto b
join servicio s on b.servicio_id = s.id
where s.id = 1/**/
;
