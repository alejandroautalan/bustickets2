import csv

localidades_csv = "localidades.csv"

with open(localidades_csv, newline='') as csvfile:
    reader = csv.DictReader(csvfile)
    sql = []
    insert_tpl = f"""INSERT INTO ciudad (id, id_gobar, provincia_id, nombre) values"""
    sql.append(insert_tpl)
    values = []
    for rpos, row in enumerate(reader, start=1):
        insert_values_tpl = f"""({rpos}, {row["id"]}, {row["provincia_id"]}, "{row["nombre"]}")"""
        values.append(insert_values_tpl)
    values = ",\n".join(values)
    sql.append(values)
    sql = "\n".join(sql)
    print(sql, ";")
